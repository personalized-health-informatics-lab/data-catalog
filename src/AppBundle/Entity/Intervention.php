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
 * @ORM\Table(name="interventions")
 */
class Intervention
{
    /**
     * @ORM\Column(type="integer", name="intervention_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $intervention_name;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $intervention_type;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $other_name;

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
     * Get name for display
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->intervention_name;
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

    /**
     * Set intervention_name
     *
     * @param string $interventionName
     * @return Intervention
     */
    public function setInterventionName($interventionName)
    {
        $this->intervention_name = $interventionName;

        return $this;
    }

    /**
     * Get intervention_name
     *
     * @return string
     */
    public function getInterventionName()
    {
        return $this->intervention_name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Intervention
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set intervention_type
     *
     * @param string $interventionType
     * @return Intervention
     */
    public function setInterventionType($interventionType)
    {
        $this->intervention_type = $interventionType;

        return $this;
    }

    /**
     * Get intervention_type
     *
     * @return string
     */
    public function getInterventionType()
    {
        return $this->intervention_type;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Intervention
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set other_name
     *
     * @param string $otherName
     * @return Intervention
     */
    public function setOtherName($otherName)
    {
        $this->other_name = $otherName;

        return $this;
    }

    /**
     * Get other_name
     *
     * @return string
     */
    public function getOtherName()
    {
        return $this->other_name;
    }

    /**
     * Set description
     *
     * @param Description $description
     * @return Intervention
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
     * @return Intervention
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
            'intervention_name' => $this->intervention_name,
            'intervention_type' => $this->intervention_type,
            'label' => $this->label,
            'other_name' => $this->other_name,
            'description' => $desc
        );
    }
}