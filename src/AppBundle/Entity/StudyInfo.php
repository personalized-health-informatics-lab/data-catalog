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
 * @ORM\Table(name="study_info")
 */
class StudyInfo
{
    /**
     * @ORM\Column(type="integer",name="study_info_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=128, nullable=true)
     */
    protected $source;

    /**
     * @ORM\Column(type="string",length=32, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="string",length=32, nullable=true)
     */
    protected $study_type;

    /**
     * @ORM\Column(type="string",length=16, nullable=true)
     */
    protected $phase;

    /**
     * @ORM\Column(type="string",length=64, nullable=true)
     */
    protected $purpose;

    /**
     * @ORM\OneToOne(targetEntity="Dataset")
     * @ORM\JoinColumn(name="dataset_uid",referencedColumnName="dataset_uid")
     */
    protected $dataset;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return StudyInfo
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return StudyInfo
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set study_type
     *
     * @param string $studyType
     * @return StudyInfo
     */
    public function setStudyType($studyType)
    {
        $this->study_type = $studyType;

        return $this;
    }

    /**
     * Get study_type
     *
     * @return string
     */
    public function getStudyType()
    {
        return $this->study_type;
    }

    /**
     * Set phase
     *
     * @param string $phase
     * @return StudyInfo
     */
    public function setPhase($phase)
    {
        $this->phase = $phase;

        return $this;
    }

    /**
     * Get phase
     *
     * @return string
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * Set purpose
     *
     * @param string $purpose
     * @return StudyInfo
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * Get purpose
     *
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * Set dataset
     *
     * @param Dataset $dataset
     * @return StudyInfo
     */
    public function setDataset(Dataset $dataset)
    {
        $this->$dataset = $dataset;

        return $this;
    }

    /**
     * Get dataset
     *
     * @return Dataset
     */
    public function getDataset()
    {
        return $this->dataset;
    }

    /**
     * Serialize all properties
     *
     * @return array
     */
    public function getAllProperties()
    {
        return array(
            'source' => $this->source,
            'status' => $this->status,
            'study_type' => $this->study_type,
            'phase' => $this->phase,
            'purpose' => $this->purpose
        );
    }
}