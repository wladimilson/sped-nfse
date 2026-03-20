<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMElement;
use DOMException;
use NFePHP\Common\Strings;

/**
 * @property Dom $dom
 * @property DOMElement $end
 * @property DOMElement $endExt
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagEnd
{
    use TraitTagEnderecoExterior;
    /**
     * Endereço simples. 
     * tag <root>/[atvEvento|dest]/end (opcional)
     */
    public function tagEnd(stdClass $std): DOMElement
    {
        $possible = [
            'CEP',
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag end -';
        $this->end = $this->dom->createElement('end');
        $this->dom->addChild(
            $this->end,
            'CEP',
            Strings::onlyNumbers($std->CEP),
            false,
            $identificador . 'CEP do endereço'
        );
        $this->dom->addChild(
            $this->end,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->end,
            'nro',
            $std->nro,
            true,
            $identificador . 'Numero do Logradouro do tomador'
        );
        $this->dom->addChild(
            $this->end,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->end,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        return $this->end;
    }

    /**
     * Endereço no exterior. 
     * tag <root>/[atvEvento|dest]/end/endExt (opcional)
     */
    public function tagEndExt(stdClass $std): DOMElement
    {
        $this->endExt = $this->tagEnderecoExterior($std, 'endExt');

        if (empty($this->end)) {
            $this->end = $this->dom->createElement('end');
            $this->end->appendChild($this->endExt);
        }
        return $this->endExt;
    }

    /**
     * Endereço nascional do destinatário referente ao IBS e à CBS. 
     * tag <root>/IBSCBS/dest/end/endNac (opcional)
     */
    public function tagEndNac(stdClass $std): DOMElement
    {
        $possible = [
            'cMun',
            'CEP',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag IBSCBS/dest/end/endNac -';
        $endNac = $this->dom->createElement('endNac');

        if (!empty($this->end)) {
            $node = $this->end->getElementsByTagName("CEP")->item(0);
            $CEP = $std->CEP;
            if (isset($node)) {
                // Remove CEP da raiz do endereço, quando referente  ao IBS e à CBS
                $CEP ??= $node->nodeValue;
                $this->end->removeChild($node);
            }

            $this->dom->addChild(
                $endNac,
                'cMun',
                Strings::onlyNumbers($std->xCpl),
                false,
                $identificador . 'Código do município'
            );
            $this->dom->addChild(
                $endNac,
                'CEP',
                $CEP,
                true,
                $identificador . 'CEP'
            );
            $node = $this->end->getElementsByTagName("xLgr")->item(0);
            $this->end->insertBefore($endNac, $node);
        } else {
            throw new DOMException("A tag end não pode ser nula/vazia");
        }

        return $this->end;
    }
}