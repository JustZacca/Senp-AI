<?php
use Rubix\ML\Classifiers\NaiveBayes;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\JSON;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\CrossValidation\Metrics\Accuracy;

class AI
{
    public $dataset;
    public $estimator;

    public function __construct($user)
    {
        $this->user =$user;
        $this->dataset = Labeled::fromIterator(new JSON($this->user->userList()));
        $this->estimator = new NaiveBayes(2.5, [
            'spam' => 0.3,
            'not spam' => 0.7,
        ]);
        
        $this->estimator->train($this->dataset);
        $this->anime = new AniList;
    }

    public function SingleMatch($id)
    {
        $ani = $this->anime;
        $ani ->query($id);
        $samples = [
            [ $ani->getFormat() , $ani->getSource(), $ani->getGenere(),$ani->getTags()]
        ];
        
        $dataset = new Unlabeled($samples);
        
        $predictions = $this->estimator->predict($dataset);
        return $predictions[0];
    }

    public function ILoveYou()
    {
        $id = $this->user->randAnime();
        if ($this->SingleMatch($id) == "Completed" | $this->SingleMatch($id) == "Watching" | $this->SingleMatch($id) == "Plan to Watch" ) {
            return $id;
        } else {
            return $this->ILoveYou();
        }
    }

    public function ListGenerator($n)
    {
        for ($i = 0; $i != $n; $i++) {
            try {
                $list[$i]["ID"] =  $this->ILoveYou();
            } catch (Exception $var) {
                return $var;
            }
        }
        return $list;
    }

    public function validity()
    {
        [$training, $testing] = $this->dataset->randomize()->split(0.8);
        

        $predictions = $this->estimator->predict($testing);

        $metric = new Accuracy();

        $score = $metric->score($predictions, $testing->labels());

        return $score*100;
    }
}
?>