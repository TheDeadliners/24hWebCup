<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;

class JwtService
{
    /**
     * @var string Secret de génération du JWT
     */
    private string $secret = '3xDLrjqdlWIQCRx577s9fsctEji3MZqTglnxDVXc3G/RZJ+ZZZM9+MGlW6mptukL';

    /**
     * @var int Durée de validité du JWT en secondes (86400 secondes = 24 heures)
     */
    private int $validity = 60 * 15;

    /**
     * Génération du JWT
     * @param array $header
     * @param array $payload
     * @param bool $validity
     * @return string
     */
    public function generate(array $header, array $payload, bool $validity = true): string {

        // Récupération de l'heure de génération du JWT
        $now = new DateTimeImmutable();

        // Lorsque validité est fausse, on évite de recalculer l'expiration du JWT (utile pour sa vérification)
        if ($validity) {
            // Calcul de l'heure d'expiration du JWT
            $expiration = $now->getTimestamp() + $this->validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $expiration;
        }

        // Encodage en base64 du header transformé en JSON
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // Nettoyage les valeurs encodées (on enlève les +, /, et =, qui peuvent corrompre l'interprétation du  JWT)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // Encodage en base6 du secret
        $base64Secret = base64_encode($this->secret);

        // Génération de la signature du JWT avec l'algorithme SHA256
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $base64Secret, true);

        // Encodage en base6 de la signature
        $base64Signature = base64_encode($signature);

        // Nettoyage les valeurs encodées (on enlève les +, /, et =, qui peuvent corrompre l'interprétation du  JWT)
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // Assemblage du JWT
        $jwt = $base64Header . '.' . $base64Payload . "." . $base64Signature;

        return $jwt;
    }

    /**
     * Démontage du JWT et récupération du header
     * @param string $token
     * @return array
     */
    public function getHeader(string $token): array {
        // Désassemblage du JWT
        $array = explode('.', $token);

        // Décodage du header en base64 et décodage du JSON
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    /**
     * Démontage du JWT et récupération du payload
     * @param string $token
     * @return array
     */
    public function getPayload(string $token): array {
        // Désassemblage du JWT
        $array = explode('.', $token);

        // Décodage du payload en base64 et décodage du JSON
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    /**
     * Vérification de la validité structurelle du JWT (header, payload, signature, ...)
     * @param string $token
     * @return bool
     */
    public function isValid(string $token): bool {
        // Regex vérifiant si le token est bien composé de 3 groupes séparé par des points, et qu'il ne contient pas de caractère interdits
        return preg_match(
                '/^[a-zA-Z0-9\-_=]+\.[a-zA-Z0-9\-_=]+\.[a-zA-Z0-9\-_=]+$/',
                $token
            ) === 1;
    }

    /**
     * Vérification de l'expiration du JWT (champ exp du payload JWT)
     * @param string $token
     * @return bool
     */
    public function hasExpired(string $token): bool {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return  $payload['exp'] < $now->getTimestamp();
    }

    /**
     * Vérification de l'intégrité du JWT (vérification de la signature et du secret)
     * @param string $token
     * @return bool
     */
    public function check(string $token): bool {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // Génération d'un token de vérification avec les même données
        // Validité doit être fausse, pour obtenir le même token, avec les même dates que le premier
        $verificationToken = $this->generate($header, $payload, false);

        // Si même contenu, même signature, alors le token est intact
        return $token === $verificationToken;
    }

    /**
     * Procédure de verification totale du JWT (conformité, expiration, intégrité)
     * @param string $token
     * @return bool
     */
    public function verifyToken(string $token): bool {
        return ($this->isValid($token) && ! $this->hasExpired($token) && $this->check($token));
    }

    /**
     * Procédure d'extraction du JWT d'une requête HTTP
     * @param Request $request
     * @return string
     */
    public function extractRequestToken(Request $request): string {
        $token = (str_replace('Bearer ', '', $request->headers->get('authorization')));
        return $token;
    }

    /**
     * Procédure d'extraction du JWT d'une requête HTTP et vérification totale
     * @param Request $request
     * @return bool
     */
    public function verifyRequestToken(Request $request): bool {
        $token = (str_replace('Bearer ', '', $request->headers->get('authorization')));
        return ($this->isValid($token) && ! $this->hasExpired($token) && $this->check($token));
    }

    /**
     * Récupération de l'utilisateur lié au JWT
     * @param String $token
     * @param UserRepository $userRepository
     * @return User
     */
    public function getUser(String $token, UserRepository $userRepository): User {
        $payload = $this->getPayload($token);
        return $userRepository->find($payload['uid']);
    }
}