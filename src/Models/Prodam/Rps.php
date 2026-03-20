<?php

namespace NFePHP\NFSe\Models\Prodam;

/**
 * Classe a montagem do RPS para a Cidade de São Paulo
 * conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Rps
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use InvalidArgumentException;
use NFePHP\NFSe\Common\Rps as RpsBase;
use stdClass;

/**
 * Propriedades para a assinatura
 * @property string $prestadorIM
 * @property string $serieRPS
 * @property string $numeroRPS
 * @property int $tomadorTipoDoc
 * @property string $tomadorCNPJCPF
 * @property int $intermediarioTipoDoc
 * @property string $intermediarioCNPJCPF
 * 
 * Propriedades da NFS-e
 * 
 * @property stdClass|array $chaveRPS
 * @property string $tipoRPS
 * @property string $dataEmissao
 * @property string $statusRPS
 * @property string $tributacaoRPS
 * @property float $valorDeducoes
 * @property float $valorPIS
 * @property float $valorCOFINS
 * @property float $valorINSS
 * @property float $valorIR
 * @property float $valorCSLL
 * @property string $codigoServico
 * @property float $aliquotaServicos
 * @property bool $issRetido
 * @property array $cpfCnpjTomador
 * @property ?string $inscricaoMunicipalTomador
 * @property ?string $inscricaoEstadualTomador
 * @property ?string $razaoSocialTomador
 * @property ?stdClass|?array $enderecoTomador
 * @property ?string $emailTomador
 * @property ?array $cpfCnpjIntermediario
 * @property ?string $inscricaoMunicipalIntermediario
 * @property ?bool $issRetidoIntermediario
 * @property ?string $emailIntermediario
 * @property string $discriminacao
 * @property ?float $valorCargaTributaria
 * @property ?float $percentualCargaTributaria
 * @property ?string $fonteCargaTributaria
 * @property ?string $codigoCEI
 * @property ?string $matriculaObra
 * @property ?string $municipioPrestacao
 * @property ?string $numeroEncapsulamento
 * @property ?float $valorTotalRecebido
 * @property ?float $valorInicialCobrado
 * @property ?float $valorFinalCobrado
 * @property ?float $valorMulta
 * @property ?float $valorJuros
 * @property float $valorIPI
 * @property bool $exigibilidadeSuspensa
 * @property bool $pagamentoParceladoAntecipado
 * @property ?string $NCM
 * @property string $NBS
 * @property ?stdClass|?array $atvEvento
 * @property ?string $cLocPrestacao
 * @property ?string $cPaisPrestacao
 * @property stdClass|array $ibsCbs
 * @property string $versaoRPS
 * @property float $valorServicos
 */
class Rps extends RpsBase
{
    private $aTp = [
        'RPS' => 'Recibo Provisório de Serviços',
        'RPS-M' => 'Recibo Provisório de Serviços proveniente de Nota Fiscal Conjugada (Mista)',
        'RPS-C' => 'Cupom'
    ];

    private $aTrib = [
        'T' => 'Tributado em São Paulo',
        'F' => 'Tributado Fora de São Paulo',
        'A' => 'Tributado em São Paulo, porém Isento',
        'B' => 'Tributado Fora de São Paulo, porém Isento',
        'M' => 'Tributado em São Paulo, porém Imune',
        'N' => 'Tributado Fora de São Paulo, porém Imune',
        'X' => 'Tributado em São Paulo, porém Exigibilidade Suspensa',
        'V' => 'Tributado Fora de São Paulo, porém Exigibilidade Suspensa',
        'P' => 'Exportação de Serviços'
    ];

    /**
     * Versão do layout usado 1 ou 2
     * @param string $versao
     */
    public function versao(string $versao): void
    {
        $versao = preg_replace('/[^0-9]/', '', $versao);
        $this->versaoRPS = $versao;
    }

    /**
     * Inscrição Municipal do Prestador do Serviço
     * @param string $im
     */
    public function prestador(string $im)
    {
        $this->prestadorIM = $im;
    }

    /**
     * Série do RPS
     * @param string $serie
     */
    public function serie(string $serie)
    {
        $serie = substr(trim($serie), 0, 5);
        $this->serieRPS = $serie;
    }

    /**
     * Numero do RPS
     * @param int $numero
     * @throws InvalidArgumentException
     */
    public function numero(int $numero)
    {
        if (!is_numeric($numero) || $numero <= 0) {
            $msg = "[$numero] não é aceito. O numero do RPS deve ser numerico maior ou igual a 1";
            throw new InvalidArgumentException($msg);
        }
        $this->numeroRPS = $numero;
    }

    /**
     * Status do RPS Normal ou Cancelado
     * @param string $status
     * @throws InvalidArgumentException
     */
    public function status(string $status): void
    {
        $status = strtoupper(trim($status));
        if (!$this->validData(['N' => 0, 'C' => 1], $status)) {
            $msg = 'O status pode ser apenas N-normal ou C-cancelado.';
            throw new InvalidArgumentException($msg);
        }
        $this->statusRPS = $status;
    }

    /**
     * Tipo do RPS
     * RPS – Recibo Provisório de Serviços
     * RPS-M – Recibo Provisório de Serviços proveniente de Nota Fiscal Conjugada (Mista);
     * RPS-C – Cupom
     *
     * @param string $tipo
     */
    public function tipo(string $tipo): void
    {
        $tipo = strtoupper(trim($tipo));
        if (!$this->validData($this->aTp, $tipo)) {
            $msg = "[$tipo] não é um codigo valido entre " . implode(',', array_keys($this->aTp)) . ".";
            throw new InvalidArgumentException($msg);
        }
        $this->tipoRPS = $tipo;
    }

    /**
     * Tributação
     * T – Tributado em São Paulo
     * F – Tributado Fora de São Paulo
     * A – Tributado em São Paulo, porém Isento
     * B – Tributado Fora de São Paulo, porém Isento
     * M – Tributado em São Paulo, porém Imune
     * N – Tributado Fora de São Paulo, porém Imune
     * X – Tributado em São Paulo, porém Exigibilidade Suspensa
     * V – Tributado Fora de São Paulo, porém Exigibilidade Suspensa
     * P – Exportação de Serviços
     *
     * @param string $tributacao
     */
    public function tributacao(string $tributacao): void
    {
        $tributacao = strtoupper(trim($tributacao));
        if (!$this->validData($this->aTrib, $tributacao)) {
            $msg = "[$tributacao] não é um código válido, ente" . implode(',', array_keys($this->aTrib));
            throw new InvalidArgumentException($msg);
        }
        $this->tributacaoRPS = $tributacao;
    }

        /**
     * Indicador de CPF/CNPJ do Tomador e intermediário para assinatura
     * 1 para CPF.
     * 2 para CNPJ.
     * 3 para Não informado
     * 
     * @param int $indDocTomador
     * @param string $cnpjcpfTomador
     * @param int $indDocIntermediario
     * @param string $cnpjcpfIntermediario
     */
    public function docTomador(int $indDocTomador, string $cnpjcpfTomador, int $indDocIntermediario, string $cnpjcpfIntermediario) {
        $this->tomadorTipoDoc = $indDocTomador;
        $this->tomadorCNPJCPF = str_pad($cnpjcpfTomador, 14, '0', STR_PAD_LEFT);

        $this->intermediarioTipoDoc = $indDocIntermediario;
        $this->intermediarioCNPJCPF = str_pad($cnpjcpfIntermediario, 14, '0', STR_PAD_LEFT);
    }

    /** 
     * Valor dos serviços
     */
    public function valorServicos(): float {
        return $this->valorTotalRecebido ?? $this->valorFinalCobrado ?? 0;
    }
}
