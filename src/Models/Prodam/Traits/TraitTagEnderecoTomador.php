<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;
use NFePHP\Common\Strings;

/**
 * @property Dom $dom
 * @property DOMElement $this->enderecoTomador
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagEnderecoTomador
{
    /**
     * Endereço do tomador. 
     * Os campos do endereço são obrigatórios apenas para tomadores pessoa jurídica (CNPJ informado). 
     * O conteúdo destes campos será ignorado caso seja fornecido um CPF/CNPJ 
     * ou a Inscrição Municipal do tomador pertença ao município de São Paulo
     * tag <root>/EnderecoTomador (opcional)
     */
    public function tagEnderecoTomador(stdClass $std): DOMElement
    {
        $possible = [
            'TipoLogradouro',
            'Logradouro',
            'NumeroEndereco',
            'ComplementoEndereco',
            'Bairro',
            'Cidade',
            'UF',
            'CEP',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag EnderecoTomador -';
        $this->enderecoTomador = $this->dom->createElement('EnderecoTomador');
        $this->dom->addChild(
            $this->enderecoTomador,
            'TipoLogradouro',
            $std->tipoLogradouro,
            false,
            $identificador . 'Tipo do endereço (Rua, Av, ...)'
        );
        $this->dom->addChild(
            $this->enderecoTomador,
            'Logradouro',
            $std->logradouro,
            false,
            $identificador . 'Endereço do tomador'
        );
        self::$dom->addChild(
            $this->enderecoTomador,
            'NumeroEndereco',
            $std->numeroEndereco,
            false,
            $identificador . 'Numero do Logradouro do tomador'
        );
        self::$dom->addChild(
            $this->enderecoTomador,
            'ComplementoEndereco',
            $std->complementoEndereco,
            false,
            $identificador . 'Complemento endereço do tomador'
        );
        self::$dom->addChild(
            $this->enderecoTomador,
            'Bairro',
            $std->Bairro,
            false,
            $identificador . 'Bairro endereço do tomador'
        );
        self::$dom->addChild(
            $this->enderecoTomador,
            'Cidade',
            Strings::onlyNumbers($std->cidade),
            true,
            $identificador . 'Cidade endereço do tomador'
        );
        self::$dom->addChild(
            $this->enderecoTomador,
            'UF',
            $std->UF,
            false,
            $identificador . 'UF endereço do tomador'
        );
        self::$dom->addChild(
            $this->enderecoTomador,
            'CEP',
            Strings::onlyNumbers($std->CEP),
            false,
            $identificador . 'CEP endereço do tomador'
        );
        return $this->enderecoTomador;
    }
}