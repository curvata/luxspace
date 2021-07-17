<?php

namespace App\Twig;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ActiveLinkMenuExtension extends AbstractExtension
{
    private ?Request $request = null;

    public function __construct(ContainerInterface $container)
    {
        $request = $container->get('request_stack')->getCurrentRequest();

        if ($request) {
            $this->request = $request;
        }
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isActive', [$this, 'isActive']),
        ];
    }
    
    /**
     * Vérifie si un lien du menu est actif
     */
    public function isActive(string $link): bool
    {
        $uri = $this->request->server->get('REQUEST_URI');
        $uriWithoutStartSlash = substr($uri, 1, strlen($uri));
        $uriFinal = $uriWithoutStartSlash;

        if (strpos($uriWithoutStartSlash, '/') > 0) {
            $uriFinal = substr($uriWithoutStartSlash, 0, strpos($uriWithoutStartSlash, '/'));
            if ($uriFinal === 'admin') {
                $uriFinal = substr(
                    substr($uriWithoutStartSlash, 1, strlen($uriWithoutStartSlash)),
                    strpos($uriWithoutStartSlash, '/'),
                    strlen($uriWithoutStartSlash)
                );

                if (strpos($uriFinal, '/') > 0) {
                    $uriFinal = substr($uriFinal, 0, strpos($uriFinal, '/'));
                }
            }
        }

        if ($link === $uriFinal) {
            return true;
        }

        return false;
    }
}
