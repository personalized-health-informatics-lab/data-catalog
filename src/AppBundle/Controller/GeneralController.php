<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\SearchResults;
use AppBundle\Entity\SearchState;
use AppBundle\Entity\Dataset;
use AppBundle\Form\Type\DatasetType;
use AppBundle\Utils\Slugger;

/**
 *  A controller handling the main search functionality, contact and About pages,
 *  dataset views, etc.
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
class GeneralController extends Controller
{
    /**
     * Produce main menu page
     *
     * @param Request The current HTTP request
     *
     * @return Response A Response instance
     *
     * @Route("/", name="main_menu")
     */
    public function menuAction(Request $request)
    {
        return $this->render('default/main_menu.html.twig');
    }

    /**
     * Performs searches and produces results pages
     *
     * @param Request The current HTTP request
     *
     * @return Response A Response instance
     *
     * @Route("/index", name="homepage")
     * @Route("/search", name="user_search_results")
     */
    public function indexAction(Request $request)
    {

        $currentSearch = new SearchState($request);

        $solr = $this->get('SolrSearchr');
        $solr->setUserSearch($currentSearch);
        $resultsFromSolr = $solr->fetchFromSolr();

        $results = new SearchResults($resultsFromSolr);

        if ($results->numResults == 0) {
            return $this->render('default/no_results.html.twig', array(
                'results' => $results,
                'currentSearch' => $currentSearch,
            ));
        } else {
            return $this->render('default/results.html.twig', array(
                'results' => $results,
                'currentSearch' => $currentSearch,
            ));
        }

    }

    /**
     * Advanced search
     *
     * @param Request $request
     *
     * @return Response A Response instance
     * @Route("/advanced_search", name="advanced_search")
     */
    public function searchAction(Request $request)
    {
        if (!$request->query->all()) {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $datasets = $qb->select('d.origin')
                ->from('AppBundle:Dataset', 'd')
                ->distinct()
                ->getQuery()->getResult();
            $origins = array();
            foreach ($datasets as $dataset) {
                $origins[] = $dataset['origin'];
            }
            return $this->render('default/advanced_search.html.twig', array(
                'origins' => $origins,
            ));
        } else {
            $keyword = '';
            $keys = ['dataset_title_txt', 'brief_descriptions_txt', 'detail_descriptions_txt', 'origin_txt', 'authors_txt', 'subject_domains_txt', 'subject_keywords_txt', 'study_purpose_txt', 'outcome_measures_txt', 'outcome_descriptions_txt', 'intervention_descriptions_txt', 'biospec_descriptions_txt', 'model_enrollment_num', 'subject_geographic_area_txt', 'subject_geographic_area_details_txt'];
            foreach ($keys as $key) {
                if ($request->query->get($key)) {
                    if (strpos($key, 'num')) {
                        $keyword .= $key . ':' . $request->query->get($key) . ' AND ';
                    } else {
                        $keyword .= $key . ':*' . $request->query->get($key) . '* AND ';
                    }
                }
            }
            $keyword = rtrim($keyword, ' AND ');
            $request->query->set('keyword', $keyword);

            return $this->indexAction($request);
        }
    }

    /**
     * Produce the About page, checking if we have an institution-
     * specific version.
     *
     * @param Request The current HTTP request
     *
     * @return Response A Response instance
     * @Route("/about", name="about")
     */
    public function aboutAction(Request $request)
    {

        if ($this->get('templating')->exists('institution/about.html.twig')) {
            return $this->render('institution/about.html.twig', array());
        } else {
            return $this->render('default/about.html.twig', array());
        }

    }


    /**
     * Produce the Contact Us page and send emails to the
     * users specified in parameters.yml
     * NOTE: The setTo() and setFrom() methods are supposed
     * to accept arrays for multiple recipients, but this appears
     * not to work.
     *
     * @param Request The current HTTP request
     *
     * @return Response A Response instance
     *
     * @Route("/contact-us", name="contact")
     */
    public function contactAction(Request $request)
    {
        $contactFormEmail = new \AppBundle\Entity\ContactFormEmail();

        // Get email addresses and institution list from parameters.yml
        $emailTo = $this->container->getParameter('contact_email_to');
        $emailFrom = $this->container->getParameter('contact_email_from');
        $affiliationOptions = $this->container->getParameter('institutional_affiliation_options');

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new \AppBundle\Form\Type\ContactFormEmailType($affiliationOptions), $contactFormEmail);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $email = $form->getData();

            // save their submission to the database first
            $em->persist($email);
            $em->flush();

            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('New Feedback about Data Catalog')
                ->setFrom($emailFrom)
                ->setTo($emailTo)
                ->setBody(
                    $this->renderView(
                        'default/feedback_email.html.twig',
                        array('msg' => $email)
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->render('default/contact_email_send_success.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        return $this->render('default/contact.html.twig', array(
            'form' => $form->createView(),
        ));

    }


    /**
     * Produce the detailed pages for individual datasets
     *
     * @param string $dataset_uid The UID of the dataset to be viewed
     * @param Request The current HTTP request
     *
     * @return Response A Response instance
     *
     * @Route("/dataset/{uid}", name="view_dataset")
     */
    public function viewAction($uid, Request $request)
    {
        $dataset = $this->getDoctrine()
            ->getRepository('AppBundle:Dataset')
            ->findOneBy(array('dataset_uid' => $uid));

        // dataset not found
        if (!$dataset) {
            throw $this->createNotFoundException(
                'No dataset matching ID "' . $uid . '"'
            );
        }
        // dataset is unpublished, and user is not admin
        if (!$dataset->getPublished() && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException(
                'You are not authorized to view this resource.');
        }


        if ($dataset->getOrigin() == 'Internal') {
            return $this->render('default/view_dataset_internal.html.twig', array(
                'dataset' => $dataset,
            ));
        } else {
            return $this->render('default/view_dataset_external.html.twig', array(
                'dataset' => $dataset,
            ));
        }
    }

}
