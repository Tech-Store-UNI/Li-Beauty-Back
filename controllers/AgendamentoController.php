<?php

require_once __DIR__ . '/../routes/Agendamento.php';

class AgendamentoController {
    public function agendar() {
        $dados = json_decode(file_get_contents("php://input"));
    }
}