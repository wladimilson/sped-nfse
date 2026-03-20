<?php

namespace NFePHP\NFSe\Models\Prodam\Traits;

use NFePHP\Common\DOMImproved;
use stdClass;
use DOMElement;
use DOMException;
use DateTime;
use DateTimeZone;

/**
 * @property DOMImproved $dom
 * @property array $errors
 * @property DOMElement $atvEvento
 * @method equilizeParameters($std, $possible)
 */
trait TraitTagAtvEvento
{
    /**
     * Informações dos Tipos de evento
     * tag <root>/infNFe/ide
     * NOTA: Ajustado para NFS-e v2
     * @param stdClass $std
     * @return DOMElement
     * @throws DOMException
     */
    public function tagAtvEvento(stdClass $std): DOMElement
    {
        $possible = [
            'xNomeEvt',
            'dtIniEvt',
            'dtFimEvt',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'Tag atvEvento - ';
        
        if (!empty($std->dtIniEvt) && !empty($std->dtFimEvt)) {
            $tze = substr($std->dtFimEvt, -5);
            $tzs = substr($std->dtIniEvt, -5);
            if ($tze !== $tzs) {
                $this->errors[] = 'A data do início do evento (dtIniEvt) não pode estar em um TIMEZONE '
                    . 'diferente da data do fim do evento (dtFimEvt).';
            }
            $tmse = DateTime::createFromFormat('Y-m-d\TH:i:sP', $std->dtFimEvt);
            $tmse->setTimezone(new DateTimeZone('UTC'));
            $tmss = DateTime::createFromFormat('Y-m-d\TH:i:sP', $std->dtIniEvt);
            $tmss->setTimezone(new DateTimeZone('UTC'));
            if ($tmss->getTimestamp() < $tmse->getTimestamp()) {
                $this->errors[] = "$identificador A data do início do evento (dtIniEvt) não pode ser menor "
                    . "que a data do fim do evento (dtFimEvt).";
            }
        }

        $this->atvEvento = $this->dom->createElement("atvEvento");
        $this->dom->addChild(
            $this->atvEvento,
            "xNomeEvt",
            $std->xNomeEvt,
            true,
            $identificador . "Nome do evento cultural, artístico, esportivo"
        );
        $this->dom->addChild(
            $this->atvEvento,
            "cNF",
            $std->cNF,
            true,
            $identificador . "Código Numérico que compõe a Chave de Acesso"
        );
        $this->dom->addChild(
            $this->atvEvento,
            "natOp",
            $std->natOp,
            true,
            $identificador . "Descrição da Natureza da Operação"
        );
        $this->dom->addChild(
            $this->atvEvento,
            "dtIniEvt",
            $std->dtIniEvt,
            true,
            $identificador . "Data de início da atividade de evento. Ano, Mês e Dia (AAAA-MM-DD)"
        );
        $this->dom->addChild(
            $this->atvEvento,
            "dtFimEvt",
            $std->dtFimEvt,
            true,
            $identificador . "Data de fim da atividade de evento. Ano, Mês e Dia (AAAA-MM-DD)"
        );
        
        return $this->atvEvento;
    }
}