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
 * @ORM\Table(name="model_info")
 */
class ModelInfo
{
    /**
     * @ORM\Column(type="integer",name="model_info_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=64, nullable=true)
     */
    protected $intervention_model;

    /**
     * @ORM\Column(type="string",length=128, nullable=true)
     */
    protected $masking;

    /**
     * @ORM\Column(type="string",length=64, nullable=true)
     */
    protected $observational_model;

    /**
     * @ORM\Column(type="string",length=16, nullable=true)
     */
    protected $allocation;

    /**
     * @ORM\Column(type="integer")
     */
    protected $enrollment;

    /**
     * @ORM\Column(type="integer")
     */
    protected $number_of_groups;

    /**
     * @ORM\Column(type="string",length=16, nullable=true)
     */
    protected $time_perspective;

    /**
     * @ORM\OneToOne(targetEntity="Dataset")
     * @ORM\JoinColumn(name="dataset_uid",referencedColumnName="dataset_uid")
     */
    protected $dataset;

    /**
     * @ORM\OneToOne(targetEntity="Description")
     * @ORM\JoinColumn(name="intervention_model_description_id",referencedColumnName="description_id")
     */
    protected $intervention_model_description;

    /**
     * @ORM\OneToOne(targetEntity="Description")
     * @ORM\JoinColumn(name="masking_description_id",referencedColumnName="description_id")
     */
    protected $masking_description;

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
     * Set intervention_model
     *
     * @param string $interventionModel
     * @return ModelInfo
     */
    public function setInterventionModel($interventionModel)
    {
        $this->intervention_model = $interventionModel;

        return $this;
    }

    /**
     * Get intervention_model
     *
     * @return string
     */
    public function getInterventionModel()
    {
        return $this->intervention_model;
    }

    /**
     * Set masking
     *
     * @param string $masking
     * @return ModelInfo
     */
    public function setMasking($masking)
    {
        $this->masking = $masking;

        return $this;
    }

    /**
     * Get masking
     *
     * @return string
     */
    public function getMasking()
    {
        return $this->masking;
    }

    /**
     * Set observational_model
     *
     * @param string $observationalModel
     * @return ModelInfo
     */
    public function setObservationalModel($observationalModel)
    {
        $this->observational_model = $observationalModel;

        return $this;
    }

    /**
     * Get observational_model
     *
     * @return string
     */
    public function getObservationalModel()
    {
        return $this->observational_model;
    }

    /**
     * Set allocation
     *
     * @param string $allocation
     * @return ModelInfo
     */
    public function setAllocation($allocation)
    {
        $this->allocation = $allocation;

        return $this;
    }

    /**
     * Get allocation
     *
     * @return string
     */
    public function getAllocation()
    {
        return $this->allocation;
    }

    /**
     * Set enrollment
     *
     * @param integer $enrollment
     * @return ModelInfo
     */
    public function setEnrollment($enrollment)
    {
        $this->enrollment = $enrollment;

        return $this;
    }

    /**
     * Get enrollment
     *
     * @return integer
     */
    public function getEnrollment()
    {
        return $this->enrollment;
    }

    /**
     * Set number_of_groups
     *
     * @param integer $numberOfGroups
     * @return ModelInfo
     */
    public function setNumberOfGroups($numberOfGroups)
    {
        $this->number_of_groups = $numberOfGroups;

        return $this;
    }

    /**
     * Get number_of_groups
     *
     * @return integer
     */
    public function getNumberOfGroups()
    {
        return $this->number_of_groups;
    }

    /**
     * Set time_perspective
     *
     * @param string $timePerspective
     * @return ModelInfo
     */
    public function setTimePerspective($timePerspective)
    {
        $this->time_perspective = $timePerspective;

        return $this;
    }

    /**
     * Get time_perspective
     *
     * @return string
     */
    public function getTimePerspective()
    {
        return $this->time_perspective;
    }

    /**
     * Set dataset
     *
     * @param Dataset $dataset
     * @return ModelInfo
     */
    public function setDataset(Dataset $dataset)
    {
        $this->dataset = $dataset;

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
     * Set intervention_model_description
     *
     * @param Description $description
     * @return ModelInfo
     */
    public function addInterventionModelDescription(Description $description)
    {
        $this->intervention_model_description = $description;

        return $this;
    }

    /**
     * Get intervention_model_description
     *
     * @return Description
     */
    public function getInterventionModelDescription()
    {
        return $this->intervention_model_description;
    }

    /**
     * Set masking_description
     *
     * @param Description $description
     * @return ModelInfo
     */
    public function addMaskingDescription(Description $description)
    {
        $this->masking_description = $description;

        return $this;
    }

    /**
     * Get masking_description
     *
     * @return Description
     */
    public function getMaskingDescription()
    {
        return $this->masking_description;
    }

    /**
     * Serialize all properties
     *
     * @return array
     */
    public function getAllProperties()
    {
        $inter_desc = is_null($this->intervention_model_description) ? null : $this->intervention_model_description->getDisplayName();
        $mask_desc = is_null($this->masking_description) ? null : $this->masking_description->getDisplayName();
        return array(
            'intervention_model' => $this->intervention_model,
            'intervention_model_description' => $inter_desc,
            'masking' => $this->masking,
            'masking_description' => $mask_desc,
            'observational_model' => $this->observational_model,
            'allocation' => $this->allocation,
            'enrollment' => $this->enrollment,
            'number_of_groups' => $this->number_of_groups,
            'time_perspective' => $this->time_perspective
        );
    }
}