<?php

require './vendor/autoload.php';

class PDF extends FPDF {
    
    function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0)
    {
        $font_angle+=90+$txt_angle;
        $txt_angle*=M_PI/180;
        $font_angle*=M_PI/180;
    
        $txt_dx=cos($txt_angle);
        $txt_dy=sin($txt_angle);
        $font_dx=cos($font_angle);
        $font_dy=sin($font_angle);
    
        $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
    }
    
    }

class InvoiceService
{
    protected $data = array();

    protected $margins = array(
        'h' => 15,
        'v' => 0,
        'footer' => 30,
    );

    protected $fontSize = array(
        'header'  => 18,
        'regular' => 10,
        'title'   => 10,
        'footer'  => 8,
        'bic'     => 4,
    );

    const HEADER_HEIGHT = 15;
    const COMPANY_CARD_WIDTH = 85;
    const LABEL_WIDTH = 92;
    const TITLE_HEIGHT = 12;

    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getPdf()
    {
        return $this->build()->Output('S');
    }

    protected function build()
    {
        $pdf = new \PDF();

        // initialization
        $pdf->AddFont('Roboto', '',   'Roboto-Regular.php');
        $pdf->AddFont('Roboto', 'B',  'Roboto-Bold.php');
        $pdf->AddFont('Roboto', 'I',  'Roboto-Italic.php');
        $pdf->AddFont('Roboto', 'BI', 'Roboto-BoldItalic.php');

        $pdf->AddPage();
        $pdf->SetMargins($this->margins['h'], $this->margins['v'], $this->margins['h']);

        // build
        $this->buildHeader($pdf);
        $this->buildImportantDates($pdf);

        $y = $pdf->GetY() + 10;
        $this->buildCompanyCard($pdf, $pdf->GetX(), $y, 'PRESTATAIRE', array(
            'name'    => 'POENSIS SARL // 80980601100023 // 6201Z',
            'address' => '1 rue Vincent Van Gogh' . "\n" . '78480 VERNEUIL-SUR-SEINE',
            'vat'     => 'FR83809806011',
        ));

        $this->buildCompanyCard($pdf, $pdf->GetPageWidth() - $this->margins['h'] - self::COMPANY_CARD_WIDTH, $y, 'CLIENT', array(
            'name'    => $this->data['customer']['name'],
            'address' => $this->data['customer']['headquarters'],
            'vat'     => $this->data['customer']['vat_identifier'],
        ));

        $this->buildTable($pdf, $this->data['costs']);

        $this->buildFooter($pdf);

        if (isset($this->data['is_canceled']) && $this->data['is_canceled'] === true) {
            $pdf->SetFontSize(150);
            $pdf->SetTextColor(200, 200, 200);
            $pdf->TextWithRotation(20,250, utf8_decode('ANNULÉE'),45,-45);
        }

        return $pdf;
    }

    protected function buildHeader(\PDF $pdf)
    {
        $pdf->SetFont('Roboto', '', $this->fontSize['header']);
        $pdf->SetX($this->margins['h']);
        $pdf->Cell(0, self::HEADER_HEIGHT, utf8_decode('FACTURE N° ' . $this->data['identifier']), 'B', 1, 'C');
    }

    protected function buildImportantDates(\PDF $pdf)
    {
        // prepare the data
        $lines = array(
            array(
                'label' => 'Date d\'émission :',
                'value' => $this->data['issuing_date'],
            ),
            array(
                'label' => 'Date d\'échéance :',
                'value' => $this->data['deadline'],
            ),
            array(
                'label' => 'Pour une prestation terminée le :',
                'value' => $this->data['service_end'],
            )
        );

        // calculate the horizontal position for centering this block
        $pdf->SetFont('Roboto', 'B', $this->fontSize['regular']);
        $labelWidth = $pdf->GetStringWidth($lines[2]['label']); // the longest label

        $pdf->SetFont('Roboto', '', $this->fontSize['regular']);
        $dateWidth  = $pdf->GetStringWidth($lines[0]['value']);

        $x = ($pdf->GetPageWidth() - ($labelWidth + $dateWidth)) / 2;

        // set the proper vertical position
        $pdf->Ln(5);

        // draw every line
        foreach ($lines as $line) {
            $pdf->setX($x);
            $pdf->SetFont('Roboto', 'B', $this->fontSize['regular']);
            $pdf->Cell($labelWidth, 5, utf8_decode($line['label']), 0, 0, 'R');

            $pdf->SetFont('Roboto', '', $this->fontSize['regular']);
            $pdf->Cell(0, 5, utf8_decode($line['value']));

            $pdf->Ln();
        }
    }

    protected function buildCompanyCard(\PDF $pdf, $x, $y, $title, array $content)
    {
        // prepare the data
        $lines = array(
            array(
                'label' => 'Dénomination sociale :',
                'value' => $content['name'],
            ),
            array(
                'label' => 'Siège social :',
                'value' => $content['address'],
            ),
            array(
                'label' => 'Numéro individuel d\'identification à la TVA :',
                'value' => $content['vat'],
            ),
        );

        // draw the title
        $pdf->SetXY($x, $y);
        $pdf->SetFont('Roboto', 'B', $this->fontSize['title']);
        $pdf->SetFillColor(244, 244, 244);
        $pdf->Cell(self::COMPANY_CARD_WIDTH, self::TITLE_HEIGHT, utf8_decode($title), 1, 0, 'C', true);
        $pdf->Ln();

        // Content
        $padding = 3;
        foreach ($lines as $line) {
            $pdf->SetX($x);
            $pdf->Cell(self::COMPANY_CARD_WIDTH, $padding, '', 'LR', 1);

            $pdf->setFont('Roboto', 'B', $this->fontSize['regular']);
            $pdf->SetX($x);
            $pdf->Cell(self::COMPANY_CARD_WIDTH, 5, '  ' . utf8_decode($line['label']), 'LR');
            $pdf->Ln();

            $pdf->setFont('Roboto', '', $this->fontSize['regular']);
            foreach (explode("\n", $line['value']) as $value) {
                $pdf->SetX($x);
                $pdf->Cell(self::COMPANY_CARD_WIDTH, 5, '  ' . utf8_decode($value), 'LR');
                $pdf->Ln();
            }
        }

        $pdf->SetX($x);
        $pdf->Cell(self::COMPANY_CARD_WIDTH, $padding, '', 'LR', 1);

        $pdf->Line($x, $pdf->GetY(), $x + self::COMPANY_CARD_WIDTH, $pdf->GetY());
    }

    protected function buildTable(\PDF $pdf, $lines)
    {
        $pdf->Ln(10);

        // headers
        $pdf->SetFont('Roboto', 'B', $this->fontSize['title']);
        $pdf->Cell(self::LABEL_WIDTH, self::TITLE_HEIGHT, utf8_decode('DESIGNATION'), 1, 0, 'C', true);

        $cellWidth = ($pdf->GetPageWidth() - (2 * $this->margins['h']) - self::LABEL_WIDTH) / 3;
        foreach (array('QUANTITE', 'UNITAIRE HT', 'TOTAL HT') as $title) {
            $pdf->Cell($cellWidth, self::TITLE_HEIGHT, utf8_decode($title), 1, 0, 'C', true);
        }

        $pdf->Ln();

        // draw the lines
        $pdf->SetFont('Roboto', '', $this->fontSize['regular']);

        $vPadding = 3;
        $hPadding = $pdf->GetStringWidth('  ');
        $lineHeight = 5;
        $sum = 0;
        foreach ($lines as $line) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // label
            $pdf->Cell(self::LABEL_WIDTH, $vPadding, '', 'LR', 1);
            $cellHeight = $vPadding;

            foreach ($this->getSplitLines($pdf, utf8_decode($line['name']), self::LABEL_WIDTH, $hPadding * 2) as $label) {
                $pdf->Cell(self::LABEL_WIDTH, $lineHeight, '  ' . $label, 'LR', 1, 'L');
                $cellHeight += $lineHeight;
            }

            $pdf->Cell(self::LABEL_WIDTH, $vPadding, '', 'LRB', 1);
            $cellHeight += $vPadding;

            // quantity
            $pdf->SetXY($x + self::LABEL_WIDTH, $y);

            if ($line['quantity'] > 0) {
                $pdf->Cell(
                    $cellWidth,
                    $cellHeight,
                    utf8_decode($line['quantity'] . (isset($line['type']) ? ' ' . $line['type'] . '  ' : '  ')),
                    1,
                    0,
                    'R'
                );
            } else {
                $pdf->SetFont('Roboto', 'I', $this->fontSize['regular']);
                $pdf->Cell($cellWidth, $cellHeight, utf8_decode('néant  '), 1, 0, 'R');
            }

            // adr
            if ($line['unity_price'] > 0) {
                $pdf->SetFont('Roboto', '', $this->fontSize['regular']);
                $pdf->Cell($cellWidth, $cellHeight, $this->formatMoney($line['unity_price']) . '  ', 1, 0, 'R');
            } else {
                $pdf->SetFont('Roboto', 'I', $this->fontSize['regular']);
                $pdf->Cell($cellWidth, $cellHeight, utf8_decode('néant  '), 1, 0, 'R');
            }

            // total
            $total = $line['quantity'] * $line['unity_price'];
            $sum += $total;
            $pdf->SetFont('Roboto', '', $this->fontSize['regular']);

            if ($line['quantity'] > 0 && $line['unity_price'] > 0) {
                $pdf->Cell($cellWidth, $cellHeight, $this->formatMoney($total) . '  ', 1, 1, 'R');
            } else {
                $pdf->Cell($cellWidth, $cellHeight, '-  ' . chr(128) . '  ', 1, 1, 'R');
            }
        }

        // draw the sub-table
        $x = $this->margins['h'] + self::LABEL_WIDTH;
        $y = $pdf->GetY() + 8;
        $pdf->Line($x, $y, $x + (3 * $cellWidth), $y);

        // sum
        $pdf->SetXY($x + $cellWidth, $y + 6);
        $pdf->SetFont('Roboto', 'B', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, 'Total HT :', 0, 0, 'R');
        $pdf->SetFont('Roboto', '', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, $this->formatMoney($sum), 0, 1, 'R');

        // VAT
        $vat = 0.2 * $sum;

        $pdf->SetX($x);
        $pdf->SetFont('Roboto', 'B', $this->fontSize['regular']);
        $pdf->Cell($pdf->GetStringWidth('Taux de TVA :') + 2, 6, 'Taux de TVA :', 0, 0, 'L');
        $pdf->SetFont('Roboto', '', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, '20%', 0, 0, 'L');

        $pdf->SetX($x + $cellWidth);
        $pdf->SetFont('Roboto', 'B', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, 'Total TVA :', 0, 0, 'R');
        $pdf->SetFont('Roboto', '', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, $this->formatMoney($vat), 0, 1, 'R');

        // total with taxes
        $pdf->SetX($x + $cellWidth);
        $pdf->SetFont('Roboto', 'B', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, 'Total TTC :', 0, 0, 'R');
        $pdf->SetFont('Roboto', '', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, $this->formatMoney($sum + $vat), 0, 1, 'R');

        // discount
        $pdf->SetY($pdf->GetY() + 8);
        $pdf->SetFont('Roboto', 'BI', $this->fontSize['regular']);
        $pdf->Cell(self::LABEL_WIDTH + (2 * $cellWidth), 6, utf8_decode('Escompte pour paiement anticipé :'), 0, 0, 'R');
        $pdf->SetFont('Roboto', 'I', $this->fontSize['regular']);
        $pdf->Cell($cellWidth, 6, utf8_decode('néant'), 0, 1, 'R');
    }

    protected function buildFooter(\PDF $pdf)
    {
        $pdf->SetXY($this->margins['h'], $pdf->GetPageHeight() - 53);
        $pdf->SetFont('Roboto', 'BI', $this->fontSize['footer']);
        $pdf->MultiCell($pdf->GetPageWidth() - (2 * $this->margins['h']), 5, utf8_decode('Réglement par virement :'), 0, 'C');

        $pdf->SetXY($this->margins['h'], $pdf->GetPageHeight() - 48);
        $pdf->SetFont('Roboto', 'I', $this->fontSize['footer']);
        $pdf->MultiCell($pdf->GetPageWidth() - (2 * $this->margins['h']), 5, utf8_decode('IBAN : FR76 3000 3018 6100 0270 0309 832 / BIC - Adresse SWIFT : SOGEFRPP'), 0, 'C');

        $pdf->Line($this->margins['h'], $pdf->GetPageHeight() - 40, $pdf->GetPageWidth() - $this->margins['h'], $pdf->GetPageHeight() - 40);

        $pdf->SetXY($this->margins['footer'], $pdf->GetPageHeight() - 36);
        $pdf->SetFont('Roboto', '', $this->fontSize['footer']);
        $pdf->MultiCell($pdf->GetPageWidth() - (2 * $this->margins['footer']), 5, utf8_decode('En cas de retard de paiement, seront exigibles, conformément à l\'article L 441­6 du code de commerce, une indemnité calculée sur la base de trois fois le taux de l\'intérêt légal en vigueur ainsi qu\'une indemnité forfaitaire pour frais de recouvrement de 40 euros.'), 0, 'C');
    }

    protected function formatMoney($number)
    {
        return number_format($number, 2, ',', ' ') . ' ' . chr(128);
    }

    protected function getSplitLines(\PDF $pdf, $str, $maxWidth, $padding = 0)
    {
        $naturalLines = explode("\n", $str);

        if (count($naturalLines) === 1) {
            $words = explode(' ', $str);
            $lines = array();

            foreach ($words as $word) {
                if (count($lines) === 0) {
                    $lines[] = $word;
                    continue;
                }

                $lastLine = end($lines) . ' ' . $word;
                if ($pdf->GetStringWidth($lastLine) + (2 * $padding) <= $maxWidth) {
                    $lines[count($lines) - 1] = $lastLine;
                } else {
                    $lines[] = $word;
                }
            }

            return $lines;
        } else {
            $lines = array();
            foreach ($naturalLines as $line) {
                $lines = array_merge($lines, $this->getSplitLines($pdf, $line, $maxWidth, $padding));
            }

            return $lines;
        }
    }

}

$params = explode(',', $argv[1]);

$service = new \InvoiceService(
    json_decode(urldecode(($params[0])), true)
    /*array(
    'customer' => array(
        'name' => 'A',
        'headquarters' => 'B',
        'vat_identifier' => 'C',
    ),
    'costs' => array(
        array(
            'name' => 'A',
            'quantity' => 1,
            'unity_price' => 400,
            //'type' => '',
        ),
        //...
    ),
    'identifier' => 'A',
    'issuing_date' => time(),
    'deadline' => time(),
    'service_end' => time(),*/
);

echo $service->getPdf();
