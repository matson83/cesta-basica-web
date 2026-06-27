<?php

test('a raiz redireciona visitantes para o login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
