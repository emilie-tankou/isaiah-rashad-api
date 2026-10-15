<?php

namespace App\Controller;

use App\Entity\TourDate;
use App\Repository\TourDateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/dates')]
final class TourDateController extends AbstractController
{
    // GET /api/dates : la liste de toutes les dates, de la plus proche à la plus lointaine
    #[Route('', name: 'api_dates_list', methods: ['GET'])]
    public function list(TourDateRepository $repository): JsonResponse
    {
        $dates = $repository->findBy([], ['date' => 'ASC']);

        return $this->json(array_map($this->toArray(...), $dates));
    }

    // GET /api/dates/5 : une seule date (404 automatique si elle n'existe pas)
    #[Route('/{id}', name: 'api_dates_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(TourDate $tourDate): JsonResponse
    {
        return $this->json($this->toArray($tourDate));
    }
        // POST /api/dates : créer une date. Réservé aux admins (voir security.yaml)
    #[Route('', name: 'api_dates_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        #[CurrentUser] User $currentUser,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        $tourDate = new TourDate();
        $tourDate->setVille($data['ville'] ?? '');
        $tourDate->setSalle($data['salle'] ?? null);
        $tourDate->setLienBilletterie($data['lienBilletterie'] ?? null);
        $tourDate->setCreatedBy($currentUser);

        if (isset($data['date'])) {
            try {
                $tourDate->setDate(new \DateTimeImmutable($data['date']));
            } catch (\Exception) {
                return $this->json(['error' => 'Format de date invalide.'], 422);
            }
        }

        $errors = $validator->validate($tourDate);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $em->persist($tourDate);
        $em->flush();

        return $this->json($this->toArray($tourDate), 201);
    }

    // PUT /api/dates/5 : modifier une date existante
    #[Route('/{id}', name: 'api_dates_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(TourDate $tourDate, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        if (isset($data['ville'])) {
            $tourDate->setVille($data['ville']);
        }
        if (array_key_exists('salle', $data)) {
            $tourDate->setSalle($data['salle']);
        }
        if (array_key_exists('lienBilletterie', $data)) {
            $tourDate->setLienBilletterie($data['lienBilletterie']);
        }
        if (isset($data['date'])) {
            try {
                $tourDate->setDate(new \DateTimeImmutable($data['date']));
            } catch (\Exception) {
                return $this->json(['error' => 'Format de date invalide.'], 422);
            }
        }

        $errors = $validator->validate($tourDate);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 422);
        }

        $em->flush();

        return $this->json($this->toArray($tourDate));
    }

    // DELETE /api/dates/5 : supprimer une date
    #[Route('/{id}', name: 'api_dates_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(TourDate $tourDate, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($tourDate);
        $em->flush();

        return $this->json(null, 204);
    }

    // Transforme une TourDate en tableau : on choisit nous-mêmes ce qui est exposé
    private function toArray(TourDate $tourDate): array
    {
        return [
            'id' => $tourDate->getId(),
            'date' => $tourDate->getDate()?->format(DATE_ATOM),
            'ville' => $tourDate->getVille(),
            'salle' => $tourDate->getSalle(),
            'lienBilletterie' => $tourDate->getLienBilletterie(),
        ];
    }
}