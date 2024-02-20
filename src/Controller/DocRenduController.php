<?php

namespace App\Controller;

use App\Entity\CompteRendu;
use App\Form\CompteRenduType1;
use App\Repository\CompteRenduRepository;
use App\Repository\DoctorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;

class DocRenduController extends AbstractController
{
    #[Route('/add_interpretation/{id}', name: 'add_interpretation')]
    public function addInterpretation(Request $request, CompteRendu $compteRendu, EntityManagerInterface $entityManager, DoctorRepository $repoMed): Response
    {
        // Retrieve the currently logged-in user
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$user) {
            throw new \LogicException('No user is logged in.');
        }
        // Retrieve the associated doctor entity using the DoctorRepository
        $doctor = $repoMed->findOneBy(['user' => $user]);

        // Check if the user is a doctor
        if (!$doctor) {
            throw new \LogicException('Logged-in user is not associated with any doctor.');
        }

        $form = $this->createForm(CompteRenduType1::class, $compteRendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compteRendu->setIsEdited(true); // Mark the compte rendu as edited
            $entityManager->flush();

            // Redirect back to the 'app_doctor' route with the updated compte rendu ID
            return $this->redirectToRoute('app_doctor', ['updated_id' => $compteRendu->getId()]);
        }

        return $this->render('med/update_compte_rendu.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/doctor', name: 'app_doctor', methods: ['GET'])]
    public function index(CompteRenduRepository $repoCompteendu, DoctorRepository $repoMed): Response
    {
        // Retrieve the currently logged-in user
        /** @var UserInterface $user */
        $user = $this->getUser();

        // Check if the user is logged in
        if (!$user) {
            throw new \LogicException('No user is logged in.');
        }

        // Retrieve the associated doctor entity using the DoctorRepository
        $doctor = $repoMed->findOneBy(['user' => $user]);

        // Check if the user is a doctor
        if (!$doctor) {
            throw new \LogicException('Logged-in user is not associated with any doctor.');
        }


        // Retrieve the ID of the associated doctor
        $doctorId = $doctor->getId();


        // Now you have the ID of the associated doctor, you can use it for further processing
        // Retrieve the list of compte rendus for the logged-in doctor
        $compteRendus = $repoCompteendu->findBy(['id_doctor' => $doctorId, 'isEdited' => false]);
        $compteRendusdone = $repoCompteendu->findBy(['id_doctor' => $doctorId, 'isEdited' => true]);

        return $this->render('med/med.html.twig', [
            'compteRendus' => $compteRendus,
            'done' => $compteRendusdone,

        ]);
    }

    #[Route('/search', name: 'app_doctor_search')]
    public function search(Request $request, CompteRenduRepository $compteRenduRepository, DoctorRepository $repoMed): Response
    {
        $query = $request->query->get('query');

        if (!$query) {
            return $this->redirectToRoute('app_doctor');
        }

        // Retrieve the currently logged-in user
        /** @var UserInterface $user */
        $user = $this->getUser();

        // Check if the user is logged in
        if (!$user) {
            throw new \LogicException('No user is logged in.');
        }

        // Retrieve the associated doctor entity using the DoctorRepository
        $doctor = $repoMed->findOneBy(['user' => $user]);

        // Check if the user is a doctor
        if (!$doctor) {
            throw new \LogicException('Logged-in user is not associated with any doctor.');
        }

        // Retrieve the ID of the associated doctor
        $doctorId = $doctor->getId();

        // Retrieve the list of compte rendus for the logged-in doctor
        $compteRendus = $compteRenduRepository->findByField($query);

        // Additional conditions for Pending and Done lists from the search results
        $compteRendusPending = [];
        $compteRendusDone = [];
        foreach ($compteRendus as $cr) {
            $idDoctor = $cr->getIdDoctor() ? $cr->getIdDoctor()->getId() : null; // Get the ID of the doctor or null
            if ($cr->getIsEdited() === false && $idDoctor === $doctorId) {
                $compteRendusPending[] = $cr;
            } elseif ($cr->getIsEdited() === true && $idDoctor === $doctorId) {
                $compteRendusDone[] = $cr;
            }
        }


        return $this->render('med/search.html.twig', [
            'compteRendusPendingwithsearch' => $compteRendusPending,
            'compteRendusDonewithsearch' => $compteRendusDone,
        ]);
    }
}