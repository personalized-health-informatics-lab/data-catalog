<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\DatasetViaApiType;
use AppBundle\Entity\Dataset;
use AppBundle\Utils\Slugger;
use AppBundle\Utils\Blacklist;


/**
 * A controller for producing JSON output
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
class APIController extends Controller
{

  /**
   *  We have several pseudo-entities that all relate back to the Person
   *  entity. We'll check this array so we know if we encounter one of them.
   */
  public $personEntities = array(
     'Author',
     'LocalExpert',
     'CorrespondingAuthor',
  );


  /**
   * Produce the JSON output
   *
   * @param string $slug The slug of a dataset, or "all"
   * @param string $_format The output format desired
   * @param Request $request The current HTTP request
   *
   * @return Response A Response instance
   *
   * @Route(
   *   "/api/Dataset/{uid}.{_format}", name="json_output_datasets",
   *   defaults={"uid": "all", "_format":"json"},
   * )
   * @Method("GET")
   */
  public function APIDatasetGetAction($uid, $_format, Request $request) {

    $em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();

    if ($uid == "all") {
      $datasets = $qb->select('d')
                     ->from('AppBundle:Dataset', 'd')
                     ->where('d.archived = 0 OR d.archived IS NULL')
                     ->andWhere('d.published = 1')
                     ->getQuery()->getResult();
    } elseif (substr($uid,0,4)==="all_"){
        $offset=(int)substr($uid,4);
        $datasets = $qb->select('d')
            ->from('AppBundle:Dataset', 'd')
            ->where('d.archived = 0 OR d.archived IS NULL')
            ->andWhere('d.published = 1')
            ->setFirstResult($offset)
            ->setMaxResults(500)
            ->getQuery()->getResult();
    }
    else {
      $datasets = $qb->select('d')
                     ->from('AppBundle:Dataset', 'd')
                     ->where('d.dataset_uid = :uid')
                     ->andWhere('d.published = 1')
                     ->andWhere('d.archived = 0 OR d.archived IS NULL')
                     ->setParameter('uid', $uid)
                     ->getQuery()->getResult();
    }

    $output_format = $request->get('output_format', 'default');

    switch ($output_format) {
      case "default":
        // default will use the entity's jsonSerialize() method
        $content = $datasets;
        break;
      case "solr":
        // for Solr
        $content = array();
        foreach ($datasets as $dataset) {
          $content[] = $dataset->serializeForSolr();
        }
        break;
      case "complete":
        $content = array();
        foreach ($datasets as $dataset) {
          $content[] = $dataset->serializeComplete();
        }
        break;
      default:
        // default will use the entity's jsonSerialize() method
        $content = $datasets;
    }

    if ($_format == "json") {
      $response = new Response();
      $response->setContent(json_encode($content));
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    }


  }


    /**
     * Produce the JSON output
     *
     * @param Request $request The current HTTP request
     *
     * @return Response A Response instance
     *
     * @Route("/api/word_map")
     * @Method("GET")
     */
    public function APIKeywordsGetAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $datasets = $qb->select('d')
            ->from('AppBundle:Dataset', 'd')
            ->where('d.published = 1')
            ->andWhere('d.archived = 0 OR d.archived IS NULL')
            ->getQuery()->getResult();

        $keyword_content = array();
        $domain_content = array();
        $last_name_content = array();
        foreach ($datasets as $dataset) {
            $keywords = $dataset->getSubjectKeywords()->getValues();
            $domains = $dataset->getSubjectDomains()->getValues();
            $authors = $dataset->getAuthors();
            foreach ($keywords as $keyword) {
                array_push($keyword_content, $keyword->getKeyword());
            }
            foreach ($domains as $domain) {
                array_push($domain_content, $domain->getSubjectDomain());
            }
            $full_name_list = array();
            foreach ($authors as $author) {
                array_push($full_name_list, $author->getFullName());
            }
            $full_name_list = $this->CleanAuthorList($full_name_list);
            foreach ($full_name_list as $author) {
                $author = str_replace(array("MD", "M.D.", "PhD", "MPH", "Jr", "Jr."), "", $author);
                $name = explode(",", $author)[0];
                $last_name=explode(" ", $name);
                $last_name = end($last_name);
                $last_name=explode(".", $last_name);
                $last_name = end($last_name);
                $last_name=explode("-", $last_name);
                $last_name = end($last_name);
                array_push($last_name_content, $last_name);
            }
        }

        $content = array("keywords" => $keyword_content, "domains" => $domain_content, "lastnames" => $last_name_content);
        $response = new Response();
        $response->setContent(json_encode($content));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function CleanAuthorList(array $full_name_list){
        $author_list = array();
        $black_list = new Blacklist();

        foreach ($full_name_list as $full_name) {
            foreach ($author_list as $author) {
                if (strpos(strtolower($author), strtolower($full_name))) {
                    continue 2;
                }
                if (strpos(strtolower($full_name), strtolower($author))) {
                    unset($author_list[$author]);
                    array_push($author_list, $full_name);
                    continue 2;
                }
            }

            if ($black_list->isValid($full_name)) {
                array_push($author_list, $full_name);
            }
        }
        return $author_list;
    }


  /**
   * Ingest dataset via API
   *
   * @param Request The current HTTP request
   *
   * @return Response A Response instance
   *
   * @Route("/api/Dataset")
   * @Method("POST")
   */
  public function APIDatasetPostAction(Request $request) {
    $submittedData = json_decode($request->getContent(), true);
    $dataset = new Dataset();
    $em = $this->getDoctrine()->getManager();
    $userCanSubmit = $this->get('security.context')->isGranted('ROLE_API_SUBMITTER');

    $datasetUid = $em->getRepository('AppBundle:Dataset')
                     ->getNewDatasetId();
    $dataset->setDatasetUid($datasetUid);

    if ($userCanSubmit) {
      $form = $this->createForm(new DatasetViaApiType($userCanSubmit, $datasetUid), $dataset, array('csrf_protection'=>false));
      $form->submit($submittedData);
      if ($form->isValid()) {
        $dataset = $form->getData();
        // enforce that all datasets ingested via the API will start out unpublished
        $dataset->setPublished(false);
        $addedEntityName = $dataset->getTitle();
        $slug = Slugger::slugify($addedEntityName);
        $dataset->setSlug($slug);

        $em->persist($dataset);
        foreach ($dataset->getAuthorships() as $authorship) {
          $authorship->setDataset($dataset);
          $em->persist($authorship);
        }
        $em->flush();

        return new Response('Dataset Successfully Added', 201);
      } else {
          $errors = $form->getErrorsAsString();
          $response = new Response(json_encode($errors), 422);
          $response->headers->set('Content-Type', 'application/json');

          return $response;
      }
    } else {
        return new Response('Unauthorized', 401);
    }
  }


  /**
   * Ingest other entities via API
   *
   * @param string $entityName The name of the new entity
   * @param Request the current HTTP request
   *
   * @return Response A Response instance
   *
   * @Route("/api/{entityName}")
   * @Method("POST")
   */
  public function APIEntityPostAction($entityName, Request $request) {
    $submittedData = json_decode($request->getContent(), true);

    if ($entityName == 'User') {
      return new Response('Users cannot be added via API', 403);
    } else {
      $addTemplate = 'add.html.twig';
    }

    $userCanSubmit = $this->get('security.context')->isGranted('ROLE_API_SUBMITTER');

    //prefix with namespaces so it can be called dynamically
    if (in_array($entityName, $this->personEntities)) {
      $newEntity = 'AppBundle\Entity\\Person';
    } else {
      $newEntity = 'AppBundle\Entity\\' . $entityName;
    }
    $newEntityFormType = 'AppBundle\Form\Type\\' . $entityName . "Type";

    $em = $this->getDoctrine()->getManager();
    if ($userCanSubmit) {
      $form = $this->createForm(new $newEntityFormType(),
                                new $newEntity(),
                                array('csrf_protection'=>false));
      $form->submit($submittedData);
      if ($form->isValid()) {
        $entity = $form->getData();

        // Create a slug using each entity's getDisplayName method
        $addedEntityName = $entity->getDisplayName();
        $slug = Slugger::slugify($addedEntityName);
        $entity->setSlug($slug);

        $em->persist($entity);
        $em->flush();

        return new Response($entityName . ': "' . $addedEntityName . '" successfully added.', 201);
      } else {
        $errors = $form->getErrorsAsString();
        $response = new Response(json_encode($errors), 422);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
      }
    } else {
      return new Response('Unauthorized', 401);
    }
  }


  /**
   * List related entities
   *
   * @param string $slug The slug of an entity, or "all"
   * @param string $_format The output format desired
   * @param Request $request The current HTTP request
   *
   * @return Response A Response instance
   *
   * @Route(
   *   "/api/{entityName}/{slug}.{_format}", name="json_output_related",
   *   defaults={"slug": "all", "_format":"json"},
   * )
   * @Method("GET")
   */
  public function APIEntityGetAction($entityName, $slug, $_format, Request $request) {
    if ($entityName == 'User') {
      return new Response('Users cannot be fetched via API', 403);
    }

    $em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();
    if (in_array($entityName, $this->personEntities)) {
      $entity = 'AppBundle\Entity\\Person';
    } else {
      $entity = 'AppBundle\Entity\\' . $entityName;
    }

    if ($slug == "all") {
      $entities = $qb->select('e')
                     ->from($entity, 'e')
                     ->getQuery()->getResult();
    } else {
      $entities = $qb->select('e')
                     ->from($entity, 'e')
                     ->where('e.slug = :slug')
                     ->setParameter('slug', $slug)
                     ->getQuery()->getResult();
    }
    for ($i = 0; $i < count($entities); $i++) {
      $entities[$i] = $entities[$i]->getAllProperties();
    }

    if ($_format == "json") {
      $response = new Response();
      $response->setContent(json_encode($entities));
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    }


  }

}
