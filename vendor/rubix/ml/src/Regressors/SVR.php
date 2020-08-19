<?php

namespace Rubix\ML\Regressors;

use Rubix\ML\Learner;
use Rubix\ML\DataType;
use Rubix\ML\Estimator;
use Rubix\ML\EstimatorType;
use Rubix\ML\Kernels\SVM\RBF;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Kernels\SVM\Kernel;
use Rubix\ML\Other\Helpers\Params;
use Rubix\ML\Other\Traits\PredictsSingle;
use Rubix\ML\Specifications\DatasetIsNotEmpty;
use Rubix\ML\Specifications\LabelsAreCompatibleWithLearner;
use Rubix\ML\Specifications\SamplesAreCompatibleWithEstimator;
use InvalidArgumentException;
use RuntimeException;
use Stringable;
use svmmodel;
use svm;

/**
 * SVR
 *
 * The Support Vector Machine Regressor (SVR) is a maximum margin algorithm for the purposes
 * of regression. Similarly to the [SVC](../classifiers/svc.md), the model produced by SVR
 * depends only on a subset of the training data, because the cost function for building the
 * model ignores any training data close to the model prediction given by parameter
 * *epsilon*. Thus, the value of epsilon defines a margin of tolerance where no penalty is
 * given to errors.
 *
 * > **Note:** This estimator requires the SVM extension which uses the libsvm engine under
 * the hood.
 *
 * References:
 * [1] C. Chang et al. (2011). LIBSVM: A library for support vector machines.
 * [2] A. Smola et al. (2003). A Tutorial on Support Vector Regression.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class SVR implements Estimator, Learner, Stringable
{
    use PredictsSingle;

    /**
     * The support vector machine instance.
     *
     * @var \svm
     */
    protected $svm;

    /**
     * The memoized hyper-parameters of the model.
     *
     * @var mixed[]
     */
    protected $params;

    /**
     * The trained model instance.
     *
     * @var \svmmodel|null
     */
    protected $model;

    /**
     * @param float $c
     * @param float $epsilon
     * @param \Rubix\ML\Kernels\SVM\Kernel|null $kernel
     * @param bool $shrinking
     * @param float $tolerance
     * @param float $cacheSize
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct(
        float $c = 1.0,
        float $epsilon = 0.1,
        ?Kernel $kernel = null,
        bool $shrinking = true,
        float $tolerance = 1e-3,
        float $cacheSize = 100.0
    ) {
        if (!extension_loaded('svm')) {
            throw new RuntimeException('SVM extension not loaded'
                . ', check PHP configuration.');
        }

        if ($c < 0.0) {
            throw new InvalidArgumentException('C must be greater'
                . " than 0, $c given.");
        }

        if ($epsilon < 0.0) {
            throw new InvalidArgumentException('Epsilon must be'
                . " greater than 0, $epsilon given.");
        }

        $kernel = $kernel ?? new RBF();

        if ($tolerance < 0.0) {
            throw new InvalidArgumentException('Tolerance must be'
                . " greater than 0, $tolerance given.");
        }

        if ($cacheSize <= 0.0) {
            throw new InvalidArgumentException('Cache size must be'
                . " greater than 0M, {$cacheSize}M given.");
        }

        $options = [
            svm::OPT_TYPE => svm::EPSILON_SVR,
            svm::OPT_C => $c,
            svm::OPT_P => $epsilon,
            svm::OPT_SHRINKING => $shrinking,
            svm::OPT_EPS => $tolerance,
            svm::OPT_CACHE_SIZE => $cacheSize,
        ];

        $options += $kernel->options();

        $svm = new svm();

        $svm->setOptions($options);

        $this->svm = $svm;

        $this->params = [
            'c' => $c,
            'epsilon' => $epsilon,
            'kernel' => $kernel,
            'shrinking' => $shrinking,
            'tolerance' => $tolerance,
            'cache_size' => $cacheSize,
        ];
    }

    /**
     * Return the estimator type.
     *
     * @return \Rubix\ML\EstimatorType
     */
    public function type() : EstimatorType
    {
        return EstimatorType::regressor();
    }

    /**
     * Return the data types that the estimator is compatible with.
     *
     * @return \Rubix\ML\DataType[]
     */
    public function compatibility() : array
    {
        return [
            DataType::continuous(),
        ];
    }

    /**
     * Return the settings of the hyper-parameters in an associative array.
     *
     * @return mixed[]
     */
    public function params() : array
    {
        return $this->params;
    }

    /**
     * Has the learner been trained?
     *
     * @return bool
     */
    public function trained() : bool
    {
        return isset($this->model);
    }

    /**
     * Train the learner with a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \InvalidArgumentException
     */
    public function train(Dataset $dataset) : void
    {
        if (!$dataset instanceof Labeled) {
            throw new InvalidArgumentException('Learner requires a'
                . ' Labeled training set.');
        }

        DatasetIsNotEmpty::check($dataset);
        SamplesAreCompatibleWithEstimator::check($dataset, $this);
        LabelsAreCompatibleWithLearner::check($dataset, $this);

        $labels = $dataset->labels();

        $data = [];

        foreach ($dataset->samples() as $i => $sample) {
            $data[] = array_merge([$labels[$i]], $sample);
        }

        $this->model = $this->svm->train($data);
    }

    /**
     * Make predictions from a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \RuntimeException
     * @return (int|float)[]
     */
    public function predict(Dataset $dataset) : array
    {
        if (!$this->model) {
            throw new RuntimeException('Estimator has not been trained.');
        }

        return array_map([$this->model, 'predict'], $dataset->samples());
    }

    /**
     * Save the model data to the filesystem.
     *
     * @param string $path
     * @throws \RuntimeException
     */
    public function save(string $path) : void
    {
        if (!$this->model) {
            throw new RuntimeException('Learner must be'
                . ' trained before saving.');
        }

        $this->model->save($path);
    }

    /**
     * Load model data from the filesystem.
     *
     * @param string $path
     */
    public function load(string $path) : void
    {
        $this->model = new svmmodel($path);
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'SVR (' . Params::stringify($this->params()) . ')';
    }
}
