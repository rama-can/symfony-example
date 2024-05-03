<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    /**
     * ContactController constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     */
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This controller handles the contact page & displays all contacts.
     * 
     * @Route("/contact", name="app_contact")
     */
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        $contacts = $this->entityManager->getRepository(Contact::class)->findAll();
        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * Create & store a new contact.
     *
     * @Route('/contact/create', name: 'app_contact_create')
     */
    #[Route('/contact/create', name: 'app_contact_create')]
    public function create(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            $this->addFlash('message', 'Contact created successfully!');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit & update contact.
     *
     * @Route("/contact/{id}/edit", name="app_contact_edit")
     */
    #[Route('/contact/{id}/edit', name: 'app_contact_edit')]
    public function edit(Request $request, $id): Response
    {
        $contact = $this->entityManager->getRepository(Contact::class)->find($id);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            $this->addFlash('message', 'Contact updated successfully!');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Deletes a contact.
     *
     * @Route("/contact/{id}/delete", name="app_contact_delete")
     */
    #[Route('/contact/{id}/delete', name: 'app_contact_delete')]
    public function delete($id): RedirectResponse
    {
        $contact = $this->entityManager->getRepository(Contact::class)->find($id);
        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        $this->addFlash('message', 'Contact deleted successfully!');
        return $this->redirectToRoute('app_contact');
    }
}
