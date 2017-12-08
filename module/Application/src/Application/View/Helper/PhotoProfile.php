<?php

namespace Application\View\Helper;

use Application\Service\Pessoa as sPessoa;
use Zend\View\Helper\AbstractHelper;

class PhotoProfile extends AbstractHelper {

    public function __invoke() {

        if ($this->getView()->identity()) {
//            $url = $this->getView()->identity()->user->urlImgPerfil;
//            if (null == $url) {
//            }
//            $url = ($this->getView()->identity()->user->tpSexo == sPessoa::TP_SEXO_FEMI) ? $this->getView()->basePath('images/profile_1.png') : $this->getView()->basePath('images/profile_2.png');
            $url = $this->getView()->basePath('img/profile_2.png');
            return $url;
        }
        return;
    }

}
