<?php

/**
 * Record
 * 
 * @autogenerated by Phalcon Developer Tools
 * @date 2019-06-12, 20:46:02
 */
class Record extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="item_id", type="integer", nullable=false)
     */
    protected $item_id;

    /**
     *
     * @var string
     * @Primary
     * @Column(column="day_id", type="string", nullable=false)
     */
    protected $day_id;

    /**
     *
     * @var integer
     * @Column(column="impressions", type="integer", nullable=true)
     */
    protected $impressions;

    /**
     *
     * @var integer
     * @Column(column="clicks", type="integer", nullable=true)
     */
    protected $clicks;

    /**
     *
     * @var integer
     * @Column(column="actions", type="integer", nullable=true)
     */
    protected $actions;

    /**
     *
     * @var string
     * @Column(column="costs", type="string", nullable=true)
     */
    protected $costs;

    /**
     *
     * @var string
     * @Column(column="_stats", type="string", nullable=true)
     */
    protected $_stats;

    /**
     *
     * @var string
     * @Column(column="created_at", type="string", nullable=true)
     */
    protected $created_at;

    /**
     *
     * @var string
     * @Column(column="updated_at", type="string", nullable=true)
     */
    protected $updated_at;

    /**
     * Method to set the value of field item_id
     *
     * @param integer $item_id
     * @return $this
     */
    public function setItemId($item_id)
    {
        $this->item_id = $item_id;

        return $this;
    }

    /**
     * Method to set the value of field day_id
     *
     * @param string $day_id
     * @return $this
     */
    public function setDayId($day_id)
    {
        $this->day_id = $day_id;

        return $this;
    }

    /**
     * Method to set the value of field impressions
     *
     * @param integer $impressions
     * @return $this
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;

        return $this;
    }

    /**
     * Method to set the value of field clicks
     *
     * @param integer $clicks
     * @return $this
     */
    public function setClicks($clicks)
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * Method to set the value of field actions
     *
     * @param integer $actions
     * @return $this
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Method to set the value of field costs
     *
     * @param string $costs
     * @return $this
     */
    public function setCosts($costs)
    {
        $this->costs = $costs;

        return $this;
    }

    /**
     * Method to set the value of field _stats
     *
     * @param string $_stats
     * @return $this
     */
    public function setStats($_stats)
    {
        $this->_stats = $_stats;

        return $this;
    }

    /**
     * Method to set the value of field created_at
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Method to set the value of field updated_at
     *
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Returns the value of field item_id
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * Returns the value of field day_id
     *
     * @return string
     */
    public function getDayId()
    {
        return $this->day_id;
    }

    /**
     * Returns the value of field impressions
     *
     * @return integer
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * Returns the value of field clicks
     *
     * @return integer
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * Returns the value of field actions
     *
     * @return integer
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Returns the value of field costs
     *
     * @return string
     */
    public function getCosts()
    {
        return $this->costs;
    }

    /**
     * Returns the value of field _stats
     *
     * @return string
     */
    public function getStats()
    {
        return $this->_stats;
    }

    /**
     * Returns the value of field created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Returns the value of field updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("records");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'records';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Record[]|Record|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Record|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
