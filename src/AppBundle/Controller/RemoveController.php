<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Dataset;
use AppBundle\Form\Type\DatasetAsAdminType;
use AppBundle\Utils\Slugger;


/**
 * A controller to remove datasets and other entities
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
 */
class RemoveController extends Controller {

  /**
   * Remove a dataset
   *
   * @param string $uid The UID of the dataset to be removed
   * @param Request $request The current HTTP request
   *
   * @return Response A Response instance
   *
   * @Route("/remove/Dataset/{uid}", defaults={"uid"=null}, name="remove_dataset")
   */
  public function removeDatasetAction($uid, Request $request) {
    $em = $this->getDoctrine()->getManager();
    $userIsAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');

    if ($uid == null) {
      $allEntities = $em->getRepository('AppBundle\Entity\Dataset')->findBy([], ['slug'=>'ASC']);
      return $this->render('default/list_of_entities_to_remove.html.twig', array(
        'entities'    => $allEntities,
        'entityName'  => 'Dataset',
        'adminPage'   => true,
        'displayName' => 'Dataset' 
      ));
    }
    $thisEntity = $em->getRepository('AppBundle\Entity\Dataset')->findOneBy(array('dataset_uid'=>$uid));
    if (!$thisEntity) {
      throw $this->createNotFoundException(
        'No dataset with UID ' . $uid . ' was found.'
      );
    }
    if ($userIsAdmin) {
      $form = $this->createForm(new DatasetAsAdminType($userIsAdmin, $uid), $thisEntity);
      $form->handleRequest($request);
      if ($form->isValid() && $userIsAdmin) {
        $em->remove($thisEntity);
        $em->flush();
        return $this->render('default/remove_success.html.twig', array(
          'entityName' => 'Dataset',
          'adminPage'  => true,
        ));
      }
   
      return $this->render('default/remove.html.twig', array(
        'form'          => $form->createView(),
        'displayName'   => 'Dataset',
        'adminPage'     => true,
        'thisEntityName'=> $thisEntity->getDisplayName(),
        'entityName'    => 'Dataset'
      ));
    }
  }




  /**
   * Remove an entity if user has admin privileges
   *
   * @param string $entityName The type of entity to be removed
   * @param string $slug The slug of the entity to be removed
   * @param Request $request The current HTTP request
   *
   * @return Response A Response instance
   *
   * @Route("/remove/{entityName}/{slug}", defaults={"slug"=null}, name="remove_entity")
   */
  public function removeEntityAction($entityName, $slug, Request $request) {
    //preface with namespace so it can be called dynamically
    if ($entityName == 'User') {
      $removeEntity = 'AppBundle\Entity\Security\\' . $entityName;
    } else {
      $removeEntity = 'AppBundle\Entity\\' . $entityName;
    }
    $entityFormType = 'AppBundle\Form\Type\\' . $entityName . "Type";
    $entityTypeDisplayName = trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $entityName));

    $em = $this->getDoctrine()->getManager();

    $userIsAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');

    if ($slug == null) {
      $allEntities = $em->getRepository($removeEntity)->findAll();
      return $this->render('default/list_of_entities_to_remove.html.twig', array(
        'entities'    => $allEntities,
        'entityName'  => $entityName,
        'adminPage'=>true,
        'displayName' => $entityTypeDisplayName
      ));
    }

    $thisEntity = $em->getRepository($removeEntity)->findOneBySlug($slug);
    
    if (!$thisEntity) {
      throw $this->createNotFoundException(
        'No entity of type ' . $entityName . ' was found matching this slug: ' . $slug
      );
    }
    if ($entityName == 'Dataset') {
      $datasetUid = $thisEntity->getDatasetUid();
      $form = $this->createForm(new DatasetAsAdminType($userIsAdmin, $datasetUid), $thisEntity);
    }
    else {
      $form = $this->createForm(new $entityFormType(), $thisEntity);
    }
    $form->handleRequest($request);
    if ($form->isValid() && $userIsAdmin) {
      $em->remove($thisEntity);
      $em->flush();
      return $this->render('default/remove_success.html.twig', array(
        'entityName'=>$entityTypeDisplayName,
        'adminPage'=>true,
      ));
    }
 
    return $this->render('default/remove.html.twig', array(
      'form'    => $form->createView(),
      'displayName'=>$entityTypeDisplayName,
      'adminPage'=>true,
      'thisEntityName'=>$thisEntity->getDisplayName(),
      'entityName' =>$entityName));
  }

}
