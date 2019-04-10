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
 * @ORM\Table(name="sponsors")
 */
class Sponsor
{
    /**
     * @ORM\Column(type="integer",name="sponsor_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=128, unique=true)
     */
    protected $agency;

    /**
     * @ORM\Column(type="string",length=128)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string",length=16, nullable=true)
     */
    protected $agency_class;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="sponsors")
     **/
    protected $datasets;

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
        return $this->agency;
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
     * Set agency
     *
     * @param string $agency
     * @return Sponsor
     */
    public function setAgency($agency)
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * Get agency
     *
     * @return string
     */
    public function getAgency()
    {
        return $this->agency;
    }

    /**
     * Set agency class
     *
     * @param string $agencyClass
     * @return Sponsor
     */
    public function setAgencyClass($agencyClass)
    {
        $this->agency_class = $agencyClass;

        return $this;
    }

    /**
     * Get agency class
     *
     * @return string
     */
    public function getAgencyClass()
    {
        return $this->agency_class;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Sponsor
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
     * Add datasets
     *
     * @param \AppBundle\Entity\Dataset $datasets
     * @return Sponsor
     */
    public function addDataset(Dataset $datasets)
    {
        $this->datasets[] = $datasets;

        return $this;
    }

    /**
     * Remove datasets
     *
     * @param \AppBundle\Entity\Dataset $datasets
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
        return array(
            'agency' => $this->agency,
            'agency_class' => $this->agency_class
        );
    }
}