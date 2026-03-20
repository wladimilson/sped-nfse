<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved;
use stdClass;
use DOMElement;
use DOMException;

/**
 * @property DOMImproved $dom
 * @property array $errors
 * @property DOMElement $this->chaveRPS
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagChaveRPS
{
    /**
     * Chave do RPS que originou a NFS-e
     * tag <root>/ChaveRPS
     * @param stdClass $std
     * @return DOMElement
     * @throws DOMException
     */
    public function tagChaveRPS(stdClass $std): DOMElement
    {
        $possible = [
            'InscricaoPrestador',
            'SerieRPS',
            'NumeroRPS',
        ];
        $require = [
            'InscricaoPrestador',
            'NumeroRPS',
        ];

        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag ChaveRPS - ';

        foreach ($require as $requiredTag) {
            if (!isset($std->{$requiredTag})) {
                $this->errors[] = "{$identificador} - {$requiredTag} Campo obrigatório!";
                continue;
            }
            if (!is_numeric($std->{$requiredTag})) {
                $this->errors[] = "{$identificador} - {$requiredTag} Campo incorreto!";
            }
        }

        $this->chaveRPS = $this->dom->createElement("ChaveRPS");
        $this->dom->addChild(
            $this->chaveRPS,
            "InscricaoPrestador",
            $std->inscricaoPrestador,
            true,
            $identificador . "Inscrição municipal do prestador de serviços"
        );
        $this->dom->addChild(
            $this->chaveRPS,
            "SerieRPS",
            $std->serieRPS,
            false,
            $identificador . "Série do RPS"
        );
        $this->dom->addChild(
            $this->chaveRPS,
            "NumeroRPS",
            $std->numeroRPS,
            true,
            $identificador . "Número do RPS"
        );
        
        return $this->chaveRPS;
    }
}