<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved;
use stdClass;
use DOMElement;
use NFePHP\Common\Strings;

/**
 * @property DOMImproved $dom
 * @property DOMElement $dest
 * @method equilizeParameters($std, $possible)
 */

trait TraitTagDest
{
    use TraitTagEnd;
    /**
     * Destinatário referente ao IBS e à CBS
     * tag <root>/IBSCBS/dest
     * ou tag <root>/IBSCBS/valores/gReeRepRes/documentos/fornec
     */
    public function tagDest(stdClass $std, string $tagRoot = 'dest'): DOMElement
    {
        $possible = [
            'CNPJ',
            'CPF',
            'NIF',
            'NaoNIF',
            'xNome',
            'email'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = "Tag {$tagRoot} -";
        $this->dest = $this->dom->createElement($tagRoot);
        if (!empty($std->CNPJ)) {
            $this->dom->addChild(
                $this->dest,
                "CNPJ",
                $std->CNPJ,
                false,
                $identificador . "CNPJ do destinatário"
            );
        } elseif (!empty($std->CPF)) {
            $this->dom->addChild(
                $this->dest,
                "CPF",
                Strings::onlyNumbers($std->CPF),
                false,
                $identificador . "CPF do destinatário"
            );
        } elseif (!empty($std->NIF)) {
            $this->dom->addChild(
                $this->dest,
                "NIF",
                $std->NIF,
                false,
                $identificador . "NIF do destinatário"
            );
        } elseif (!empty($std->CPF)) {
            $this->dom->addChild(
                $this->dest,
                "NaoNIF",
                $std->NaoNIF,
                false,
                $identificador . "Tipo do motivo para não informação do NIF"
            );
        }
        $this->dom->addChild(
            $this->dest,
            "xNome",
            $std->xNome,
            true,
            $identificador . "Razão Social ou Nome do destinatário"
        );
        
        $this->dom->addChild(
            $this->dest,
            "email",
            $std->email,
            false,
            $identificador . "Endereço eletrônico do destinatário"
        );
        return $this->dest;
    }

    /**
     * Endereço do destinatário
     * tag <root>/IBSCBS/dest/end
     */
    public function tagEndDest(): DOMElement
    {
        if (!empty($this->end)) {
            $node = $this->dest->getElementsByTagName("email")->item(0);
            $this->dest->insertBefore($this->end, $node);
        }
        
        return $this->end;
    }

}