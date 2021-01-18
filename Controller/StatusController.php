<?php

declare(strict_types=1);

namespace Wakeapp\Bundle\SystemStatusBundle\Controller;

use Wakeapp\Bundle\SystemStatusBundle\Service\AuthService;
use Wakeapp\Bundle\SystemStatusBundle\UseCase\StatusAction\StatusActionHandler;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusController
{
    /**
     * @param Request $request
     * @param AuthService $authService
     * @param StatusActionHandler $handler
     * @return Response
     * @throws JsonException
     */
    public function statusAction(Request $request, AuthService $authService, StatusActionHandler $handler): Response
    {
        if ($authService->authorize($request->get('api_key', '')) === false) {
            return new Response();
        }

        $result = $handler->handle($request->get('component', ''));

        return new JsonResponse($result);
    }
}
