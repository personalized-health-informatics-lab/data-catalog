<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 *
 *   This file is part of the Data Catalog project.
 *   Copyright (C) 2016 NYU Health Sciences Library
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @ORM\Entity
 * @ORM\Table(name="outcomes")
 */
class Outcome
{
    /**
     * @ORM\Column(type="integer", name="outcome_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=3000, nullable=true)
     */
    protected $measure;

    /**
     * @ORM\Column(type="string",length=500, nullable=true)
     */
    protected $time_frame;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="interventions")
     **/
    protected $datasets;

    /**
     * @ORM\OneToOne(targetEntity="Description")
     * @ORM\JoinColumn(name="description_id",referencedColumnName="description_id")
     */
    protected $description;

    public function __construct()
    {
        $this->datasets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setMeasure($measure)
    {
        $this->measure = $measure;

        return $this;
    }

    public function getMeasure()
    {
        return $this->measure;
    }

    public function setTimeFrame($timeFrame)
    {
        $this->time_frame = $timeFrame;

        return $this;
    }

    public function getTimeFrame()
    {
        return $this->time_frame;
    }

    /**
     * Set description
     *
     * @param Description $description
     * @return Outcome
     */
    public function setDescription(Description $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add datasets
     *
     * @param Dataset $datasets
     * @return Outcome
     */
    public function addDataset(Dataset $datasets)
    {
        $this->datasets[] = $datasets;

        return $this;
    }

    /**
     * Remove datasets
     *
     * @param Dataset $datasets
     */
    public function removeDataset(Dataset $datasets)
    {
        $this->datasets->removeElement($datasets);
    }

    /**
     * Get datasets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDatasets()
    {
        return $this->datasets;
    }

    /**
     * Serialize all properties
     *
     * @return array
     */
    public function getAllProperties()
    {
        $desc = is_null($this->description) ? null : $this->description->getDisplayName();
        return array(
            'measure' => $this->measure,
            'time_frame' => $this->time_frame,
            'description' => $desc
        );
    }
}