<?php

namespace Rubix\ML\AnomalyDetectors;

use Rubix\ML\Learner;
use Rubix\ML\Ranking;
use Rubix\ML\Estimator;
use Rubix\ML\Persistable;
use Rubix\ML\EstimatorType;
use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Graph\Trees\KDTree;
use Rubix\ML\Graph\Trees\Spatial;
use Rubix\ML\Other\Helpers\Stats;
use Rubix\ML\Other\Helpers\Params;
use Rubix\ML\Other\Traits\RanksSingle;
use Rubix\ML\Other\Traits\PredictsSingle;
use Rubix\ML\Specifications\DatasetIsNotEmpty;
use Rubix\ML\Specifications\DatasetHasDimensionality;
use Rubix\ML\Specifications\SamplesAreCompatibleWithEstimator;
use InvalidArgumentException;
use RuntimeException;
use Stringable;

use const Rubix\ML\EPSILON;

/**
 * Local Outlier Factor
 *
 * Local Outlier Factor (LOF) measures the local deviation of density of an unknown
 * sample with respect to its *k* nearest neighbors from the training set. As such,
 * LOF only considers the local region (or *neighborhood*) of an unknown sample
 * which enables it to detect anomalies within individual clusters of data.
 *
 * References:
 * [1] M. M. Breunig et al. (2000). LOF: Identifying Density-Based Local Outliers.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class LocalOutlierFactor implements Estimator, Learner, Ranking, Persistable, Stringable
{
    use PredictsSingle, RanksSingle;

    /**
     * The default minimum anomaly score for a sample to be flagged.
     *
     * @var float
     */
    protected const DEFAULT_THRESHOLD = 1.5;

    /**
     * The number of nearest neighbors to consider a local region.
     *
     * @var int
     */
    protected $k;

    /**
     * The percentage of outliers that are assumed to be present in the
     * training set.
     *
     * @var float|null
     */
    protected $contamination;

    /**
     * The k-d tree used for nearest neighbor queries.
     *
     * @var \Rubix\ML\Graph\Trees\Spatial
     */
    protected $tree;

    /**
     * The precomputed k distances between each training sample and its kth
     * nearest neighbor.
     *
     * @var float[]
     */
    protected $kdistances = [
        //
    ];

    /**
     * The precomputed local reachability densities of the training set.
     *
     * @var float[]
     */
    protected $lrds = [
        //
    ];

    /**
     * The local outlier factor threshold used by the decision function.
     *
     * @var float|null
     */
    protected $threshold;

    /**
     * The dimensionality of the training set.
     *
     * @var int|null
     */
    protected $featureCount;

    /**
     * @param int $k
     * @param float|null $contamination
     * @param \Rubix\ML\Graph\Trees\Spatial|null $tree
     * @throws \InvalidArgumentException
     */
    public function __construct(int $k = 20, ?float $contamination = null, ?Spatial $tree = null)
    {
        if ($k < 1) {
            throw new InvalidArgumentException('At least 1 neighbor'
                . " is required to form a local region, $k given.");
        }

        if (isset($contamination) and ($contamination < 0.0 or $contamination > 0.5)) {
            throw new InvalidArgumentException('Contamination must be'
                . " between 0 and 0.5, $contamination given.");
        }

        $this->k = $k;
        $this->contamination = $contamination;
        $this->tree = $tree ?? new KDTree();
    }

    /**
     * Return the estimator type.
     *
     * @return \Rubix\ML\EstimatorType
     */
    public function type() : EstimatorType
    {
        return EstimatorType::anomalyDetector();
    }

    /**
     * Return the data types that the estimator is compatible with.
     *
     * @return \Rubix\ML\DataType[]
     */
    public function compatibility() : array
    {
        return $this->tree->kernel()->compatibility();
    }

    /**
     * Return the settings of the hyper-parameters in an associative array.
     *
     * @return mixed[]
     */
    public function params() : array
    {
        return [
            'k' => $this->k,
            'contamination' => $this->contamination,
            'tree' => $this->tree,
        ];
    }

    /**
     * Has the learner been trained?
     *
     * @return bool
     */
    public function trained() : bool
    {
        return !$this->tree->bare() and $this->kdistances and $this->lrds;
    }

    /**
     * Return the base spatial tree instance.
     *
     * @var \Rubix\ML\Graph\Trees\Spatial
     */
    public function tree() : Spatial
    {
        return $this->tree;
    }

    /**
     * Train the learner with a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     */
    public function train(Dataset $dataset) : void
    {
        DatasetIsNotEmpty::check($dataset);
        SamplesAreCompatibleWithEstimator::check($dataset, $this);

        $labels = range(0, $dataset->numRows() - 1);

        $dataset = Labeled::quick($dataset->samples(), $labels);

        $this->tree->grow($dataset);

        $this->kdistances = $this->lrds = [];

        $iHat = $dHat = [];

        foreach ($dataset->samples() as $sample) {
            [$samples, $indices, $distances] = $this->tree->nearest($sample, $this->k);

            $iHat[] = $indices;
            $dHat[] = $distances;

            $this->kdistances[] = end($distances);
        }

        $this->lrds = array_map([$this, 'localReachabilityDensity'], $iHat, $dHat);

        if (isset($this->contamination)) {
            $lofs = array_map([$this, 'localOutlierFactor'], $dataset->samples());

            $threshold = Stats::quantile($lofs, 1.0 - $this->contamination);
        }

        $this->threshold = $threshold ?? self::DEFAULT_THRESHOLD;

        $this->featureCount = $dataset->numColumns();
    }

    /**
     * Make predictions from a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return int[]
     */
    public function predict(Dataset $dataset) : array
    {
        return array_map([$this, 'decide'], $this->rank($dataset));
    }

    /**
     * Return the anomaly scores assigned to the samples in a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \RuntimeException
     * @return float[]
     */
    public function rank(Dataset $dataset) : array
    {
        if ($this->tree->bare() or !$this->featureCount) {
            throw new RuntimeException('Estimator has not been trained.');
        }

        DatasetHasDimensionality::check($dataset, $this->featureCount);

        return array_map([$this, 'localOutlierFactor'], $dataset->samples());
    }

    /**
     * Calculate the local outlier factor of a given sample given its k
     * nearest neighbors.
     *
     * @param (int|float)[] $sample
     * @return float
     */
    protected function localOutlierFactor(array $sample) : float
    {
        [$samples, $indices, $distances] = $this->tree->nearest($sample, $this->k);

        $lrd = $this->localReachabilityDensity($indices, $distances);

        $ratios = [];

        foreach ($indices as $index) {
            $ratios[] = $this->lrds[$index] / $lrd;
        }

        return Stats::mean($ratios);
    }

    /**
     * Calculate the local reachability density of a sample given its
     * distances to its k nearest neighbors.
     *
     * @param int[] $indices
     * @param float[] $distances
     * @return float
     */
    protected function localReachabilityDensity(array $indices, array $distances) : float
    {
        $kdistances = [];

        foreach ($indices as $index) {
            $kdistances[] = $this->kdistances[$index];
        }

        $rds = array_map('max', $distances, $kdistances);

        return 1.0 / (Stats::mean($rds) ?: EPSILON);
    }

    /**
     * The decision function.
     *
     * @param float $score
     * @return int
     */
    protected function decide(float $score) : int
    {
        return $score > $this->threshold ? 1 : 0;
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'Local Outlier Factor (' . Params::stringify($this->params()) . ')';
    }
}
