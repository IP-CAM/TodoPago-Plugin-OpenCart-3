<?php

class ControllerExtensionTodopagoTodopago extends Controller
{

    public function index()
    {

        // set title of the page
        $this->document->setTitle("Fallo en la transaccion");

        // define template file
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/todopago/todopago_fail.twig')) {
            $this->template = $this->config->get('config_template') . '/template/todopago/todopago_fail.twig';
        } else {
            $this->template = 'default/template/todopago/todopago-fail.tpl';
        }

        // define children templates
        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        // set data to the variable
        $data['my_custom_text'] = "Fallo en la transaccion.";

        // call the "View" to render the output
        $this->response->setOutput($this->render());
    }

    public function cupon()
    {
        // set title of the page
        $this->document->setTitle("Pago con Cupon");

        // define template file
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/todopago/todopago_cupon.twig')) {
            $this->template = $this->config->get('config_template') . '/template/todopago/todopago_cupon.twig';
        } else {
            $this->template = 'default/template/todopago/todopago-cupon.twig';
        }

        // define children templates
        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        // set data to the variable
        $data['my_custom_text'] = "Pago con Cupon.";

        // call the "View" to render the output
        $this->response->setOutput($this->render());
    }

    public function prueba()
    {
        //$this->response->redirect($this->url->link("todopago/todopago/cupon&nroop=$nroop&venc=$venc&total=$total&code=$code&tipocode=$tipocode&empresa=&empresa"));
        $this->response->redirect($this->url->link('todopago/todopago/cupon', 'nroop=$nroop&venc=$venc&total=$total&code=$code&tipocode=$tipocode&empresa=$empresa', 'SSL'));
    }

    public function codebar()
    {
        $tipo_code = $_GET['tipo_code'];
        $bar = $_GET['bar'];
        require_once('CodeBar/BCGFontFile.php');
        require_once('Codebar/BCGColor.php');
        require_once('Codebar/BCGDrawing.php');
        // The arguments are R, G, and B for color.
        $colorFont = new BCGColor(0, 0, 0);
        $colorBack = new BCGColor(255, 255, 255);


        $code = $this->codeBarFactory($tipo_code); // Or another class name from the manual
        $code->setScale(2); // Resolution
        $code->setThickness(30); // Thickness
        $code->setForegroundColor($colorFont); // Color of bars
        $code->setBackgroundColor($colorBack); // Color of spaces

        $code->parse($bar); // Text

        $drawing = new BCGDrawing('', $colorBack);
        $drawing->setBarcode($code);

        $drawing->draw();

        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
    }

    private function codeBarFactory($code)
    {
        $var = null;
        switch ($code) {
            case 'CODE_128':
                require_once('Codebar/BCGcode128.php');
                $var = new BCGcode128();
                break;
            case'INTERLEAVED_2_OF_5':
                require_once('Codebar/BCGi25.php');
                $var = new BCGi25();
                break;
        }
        return $var;
    }

}
