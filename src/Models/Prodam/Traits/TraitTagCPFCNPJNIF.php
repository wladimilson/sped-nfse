<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved;
use stdClass;
use DOMElement;
use NFePHP\Common\Strings;

/**
 * @property DOMImproved $dom
 * @property DOMElement $CPFCNPJIntermediario
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagCPFCNPJNIF
{
    /**
     * CPF|CNPJ do intermediário de serviço
     * tag <root>/CPFCNPJIntermediario (opcional)
     */
    public function tagCPFCNPJNIF(stdClass $std, string $tagRoot = 'CPFCNPJIntermediario'): DOMElement
    {
        $possible = [
            'CNPJ',
            'CPF',
            'NIF',
            'NaoNIF',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = "TAG {$tagRoot} -";
        $tag = $this->dom->createElement($tagRoot);
        if (!empty($std->CNPJ)) {
            $this->dom->addChild(
                $tag,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'CNPJ'
            );
        } elseif (!empty($std->CPF)) {
            $this->dom->addChild(
                $tag,
                'CPF',
                Strings::onlyNumbers($std->CPF),
                true,
                $identificador . 'CPF'
            );
        } elseif (!empty($std->NIF)) {
            $this->dom->addChild(
                $tag,
                'NIF',
                $std->NIF,
                true,
                $identificador . 'NIF'
            );
        } elseif (!empty($std->CPF)) {
            $this->dom->addChild(
                $tag,
                'NaoNIF',
                $std->NaoNIF,
                true,
                $identificador . 'Tipo do motivo para não informação do NIF'
            );
        }
        $this->{$tagRoot} = $tag;
        return $this->{$tagRoot};
    }
}