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
 * @ORM\Table(name="oversight_info")
 */
class OversightInfo
{
    /**
     * @ORM\Column(type="integer",name="oversight_info_id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $has_dmc;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_fda_regulated_drug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_fda_regulated_device;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_us_export;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_unapproved_device;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_ppsd;

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
     * Set has_dmc
     *
     * @param boolean $hasDmc
     * @return OversightInfo
     */
    public function setHasDmc($hasDmc)
    {
        $this->has_dmc = $hasDmc;

        return $this;
    }

    /**
     * Get has_dmc
     *
     * @return boolean
     */
    public function getHasDmc()
    {
        return $this->has_dmc;
    }

    /**
     * Set is_fda_regulated_drug
     *
     * @param boolean $fdaDrug
     * @return OversightInfo
     */
    public function setIsFdaRegulatedDrug($fdaDrug)
    {
        $this->is_fda_regulated_drug = $fdaDrug;

        return $this;
    }

    /**
     * Get is_fda_regulated_drug
     *
     * @return boolean
     */
    public function getIsFdaRegulatedDrug()
    {
        return $this->is_fda_regulated_drug;
    }

    /**
     * Set is_fda_regulated_device
     *
     * @param boolean $fdaDevice
     * @return OversightInfo
     */
    public function setIsFdaRegulatedDevice($fdaDevice)
    {
        $this->is_fda_regulated_device = $fdaDevice;

        return $this;
    }

    /**
     * Get is_fda_regulated_device
     *
     * @return boolean
     */
    public function getIsFdaRegulatedDevice()
    {
        return $this->is_fda_regulated_device;
    }

    /**
     * Set is_us_export
     *
     * @param boolean $usExport
     * @return OversightInfo
     */
    public function setIsUsExport($usExport)
    {
        $this->is_us_export = $usExport;

        return $this;
    }

    /**
     * Get is_us_export
     *
     * @return boolean
     */
    public function getIsUsExport()
    {
        return $this->is_us_export;
    }

    /**
     * Set is_unapproved_device
     *
     * @param boolean $unapprovedDevice
     * @return OversightInfo
     */
    public function setIsUnapprovedDevice($unapprovedDevice)
    {
        $this->is_unapproved_device = $unapprovedDevice;

        return $this;
    }

    /**
     * Get is_unapproved_device
     *
     * @return boolean
     */
    public function getIsUnapprovedDevice()
    {
        return $this->is_unapproved_device;
    }

    /**
     * Set is_ppsd
     *
     * @param boolean $ppsd
     * @return OversightInfo
     */
    public function setIsPpsd($ppsd)
    {
        $this->is_ppsd = $ppsd;

        return $this;
    }

    /**
     * Get is_ppsd
     *
     * @return boolean
     */
    public function getIsPpsd()
    {
        return $this->is_ppsd;
    }


    /**
     * Set dataset
     *
     * @param Dataset $dataset
     * @return OversightInfo
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
            'has_dmc' => $this->has_dmc,
            'is_fda_regulated_drug' => $this->is_fda_regulated_drug,
            'is_fda_regulated_device' => $this->is_fda_regulated_device,
            'is_us_export' => $this->is_us_export,
            'is_unapproved_device' => $this->is_unapproved_device,
            'is_ppsd' => $this->is_ppsd
        );
    }
}