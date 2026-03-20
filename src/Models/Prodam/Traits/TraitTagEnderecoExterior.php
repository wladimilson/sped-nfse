<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;

/**
 * @property Dom $dom
 * @property DOMElement $enderecoExterior
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagEnderecoExterior
{
    /**
     * Tipo endereço no exterior. 
     * tag <root>/[EnderecoTomador|end]/[EnderecoExterior|endExt] (opcional)
     * NOTA: Em conformidade com NFS-e v2
     */
    public function tagEnderecoExterior(stdClass $std, string $tagRoot = 'EnderecoExterior'): DOMElement
    {
        $possible = [
            'cPais',
            'cEndPost',
            'xCidade',
            'xEstProvReg',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag EnderecoExterior -';
        $this->enderecoExterior = $this->dom->createElement($tagRoot);
        $this->dom->addChild(
            $this->enderecoExterior,
            'cPais',
            $std->cPais,
            true,
            $identificador . 'Código do país (Tabela de Países ISO)'
        );
        $this->dom->addChild(
            $this->enderecoExterior,
            'cEndPost',
            $std->cEndPost,
            true,
            $identificador . 'Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço'
        );
        $this->dom->addChild(
            $this->enderecoExterior,
            'xCidade',
            $std->xCidade,
            true,
            $identificador . 'Nome da cidade no exterior do prestador do serviço'
        );
        $this->dom->addChild(
            $this->enderecoExterior,
            'xEstProvReg',
            $std->xEstProvReg,
            true,
            $identificador . 'Estado, província ou região da cidade no exterior do prestador do serviço'
        );
        return $this->enderecoExterior;
    }
}