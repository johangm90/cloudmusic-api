<?php

declare(strict_types=1);

namespace App\Controller;

use Metowolf\Meting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    private const SOURCES = ['netease', 'tencent', 'xiami', 'kugou', 'baidu'];

    private function resolveSource(?string $source): string
    {
        if ($source !== null && in_array($source, self::SOURCES, true)) {
            return $source;
        }

        return 'tencent';
    }

    private function api(?string $source): Meting
    {
        return new Meting($this->resolveSource($source));
    }

    private function jsonResponse(string $payload): Response
    {
        $response = new Response($payload, Response::HTTP_OK, [
            'Content-Type' => 'application/json; charset=utf-8',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'X-Requested-With',
        ]);

        return $response;
    }

    #[Route('/v1/search/{keyword}/{page}/{limit}', methods: ['GET'])]
    #[Route('/{source}/search/{keyword}/{page}/{limit}', methods: ['GET'])]
    public function search(string $keyword, int $page, int $limit, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->search($keyword, [
            'page' => $page,
            'limit' => $limit,
        ]);

        return $this->jsonResponse($result);
    }

    #[Route('/v1/song/{id}', methods: ['GET'])]
    #[Route('/{source}/song/{id}', methods: ['GET'])]
    public function song(string $id, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->song($id);

        return $this->jsonResponse($result);
    }

    #[Route('/v1/album/{id}', methods: ['GET'])]
    #[Route('/v1/album/{id}/', methods: ['GET'])]
    #[Route('/{source}/album/{id}', methods: ['GET'])]
    #[Route('/{source}/album/{id}/', methods: ['GET'])]
    public function album(string $id, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->album($id);

        return $this->jsonResponse($result);
    }

    #[Route('/v1/artist/{id}', methods: ['GET'])]
    #[Route('/v1/artist/{id}/', methods: ['GET'])]
    #[Route('/{source}/artist/{id}', methods: ['GET'])]
    #[Route('/{source}/artist/{id}/', methods: ['GET'])]
    public function artist(string $id, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->artist($id);

        return $this->jsonResponse($result);
    }

    #[Route('/v1/playlist/{id}', methods: ['GET'])]
    #[Route('/{source}/playlist/{id}', methods: ['GET'])]
    public function playlist(string $id, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->playlist($id);

        return $this->jsonResponse($result);
    }

    #[Route('/v1/lyric/{id}', methods: ['GET'])]
    #[Route('/{source}/lyric/{id}', methods: ['GET'])]
    public function lyric(string $id, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->lyric($id);

        return $this->jsonResponse($result);
    }

    #[Route('/v1/pic/{id}', methods: ['GET'])]
    #[Route('/{source}/pic/{id}', methods: ['GET'])]
    public function pic(string $id, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->pic($id);
        $data = json_decode($result, true);

        if (is_array($data) && !empty($data['url'])) {
            return new RedirectResponse($data['url']);
        }

        throw $this->createNotFoundException();
    }

    #[Route('/v1/url/{id}/{br}', methods: ['GET'])]
    #[Route('/{source}/url/{id}/{br}', methods: ['GET'])]
    public function url(string $id, string $br, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->url($id, $br);

        return $this->jsonResponse($result);
    }

    #[Route('/v1/play/{id}/{br}', methods: ['GET'])]
    #[Route('/{source}/play/{id}/{br}', methods: ['GET'])]
    public function play(string $id, string $br, ?string $source = null): Response
    {
        $result = $this->api($source)->format(true)->url($id, $br);
        $data = json_decode($result, true);

        if (is_array($data) && !empty($data['url'])) {
            return new RedirectResponse($data['url']);
        }

        throw $this->createNotFoundException();
    }
}
