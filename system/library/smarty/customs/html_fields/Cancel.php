<?php

require_once __DIR__.'/Button.php';

class HtmlForm_CancelField extends HtmlForm_ButtonField
{
	protected function reinit(){ parent::reinit(); $this->type = 'button'; }
}

?>