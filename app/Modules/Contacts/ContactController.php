<?php

namespace App\Modules\Contacts;

use App\Core\Request;
use App\Core\Response;

class ContactController
{
    private $service;

    public function __construct()
    {
        $this->service = new ContactService();
    }

    public function create()
    {
        $request = Request::capture();
        $data = $request->all();

        $validated = ContactRequest::validateCreate($data);
        $id = $this->service->create($validated);

        Response::json(['message' => 'Contacto criado com sucesso', 'id' => $id], 201);
    }

    public function getAll()
    {
        $contacts = $this->service->getAll();
        Response::json($contacts);
    }

    public function getById(int $id)
    {
        $contact = $this->service->getById($id);

        if (!$contact) {
            Response::json(['error' => 'Contacto não encontrado'], 404);
        }

        Response::json($contact);
    }

    public function delete(int $id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            Response::json(['error' => 'Não foi possível apagar o contacto'], 400);
        }

        Response::json(['message' => 'Contacto apagado com sucesso']);
    }

    public function markAsViewed(int $id)
    {
        $updated = $this->service->markAsViewed($id);

        if (!$updated) {
            Response::json(['error' => 'Não foi possível marcar como visualizado'], 400);
        }

        Response::json(['message' => 'Contacto marcado como visualizado']);
    }
}
