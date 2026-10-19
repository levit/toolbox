<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use InvalidArgumentException;
use Softtan\Toolbox\Helpers\Str;

class StrTest extends TestCase
{
    public function testTruncar(): void
    {
        $texto = 'Desenvolvimento de software de alta performance';

        // Truncamento padrão (limite 20)
        $this->assertSame('Desenvolvimento de s...', Str::truncar($texto, 20));

        // Limite superior ao comprimento não altera o texto
        $this->assertSame($texto, Str::truncar($texto, 100));

        // Sufixo customizado
        $this->assertSame('Desenvolvimento [leia mais]', Str::truncar($texto, 15, ' [leia mais]'));

        // Texto com caracteres multibyte (UTF-8)
        $textoUtf8 = 'Atenção às alterações de trânsito';
        $this->assertSame('Atenção...', Str::truncar($textoUtf8, 7));

        // Vazio ou nulo
        $this->assertNull(Str::truncar(null));
        $this->assertNull(Str::truncar(''));
    }

    public function testSlug(): void
    {
        $this->assertSame('ola-mundo-2026', Str::slug('Olá Mundo 2026!'));
        $this->assertSame('desenvolvimento-de-software', Str::slug('Desenvolvimento de Software'));
        $this->assertSame('atencao-cuidado', Str::slug('Atenção & Cuidado'));
        $this->assertSame('atencao-e-cuidado', Str::slug('Atenção e Cuidado'));
        $this->assertSame('palavras_com_underline', Str::slug('Palavras com Underline', '_'));
        $this->assertNull(Str::slug(null));
        $this->assertNull(Str::slug(''));
    }

    public function testRemoverAcentos(): void
    {
        $this->assertSame('Cafe com Acucar e Chao', Str::removerAcentos('Café com Açúcar e Chão'));
        $this->assertSame('Joao da Silva', Str::removerAcentos('João da Silva'));

        // Tratamento de apóstrofo substituído por espaço ou mantido
        $this->assertSame('D Avila', Str::removerAcentos("D'Ávila", true));
        $this->assertSame('DAvila', Str::removerAcentos("D'Ávila", false));

        $this->assertNull(Str::removerAcentos(null));
        $this->assertSame('', Str::removerAcentos(''));
    }

    public function testGerarLocalizador(): void
    {
        // Padrão: 6 caracteres, apenas letras
        $loc1 = Str::gerarLocalizador();
        $this->assertSame(6, strlen($loc1));
        $this->assertMatchesRegularExpression('/^[A-Z]{6}$/', $loc1);

        // Sem letras confusas (I, O, Q)
        $this->assertFalse(str_contains($loc1, 'I'));
        $this->assertFalse(str_contains($loc1, 'O'));
        $this->assertFalse(str_contains($loc1, 'Q'));

        // Tamanho 10 com números (apenasLetras = false)
        $loc2 = Str::gerarLocalizador(10, false);
        $this->assertSame(10, strlen($loc2));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{10}$/', $loc2);

        // Sem números confusos (0, 1)
        $this->assertFalse(str_contains($loc2, '0'));
        $this->assertFalse(str_contains($loc2, '1'));
    }

    public function testGerarLocalizadorTamanhoInvalidoLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O tamanho deve ser maior que zero.');
        Str::gerarLocalizador(0);
    }

    public function testNameRandom(): void
    {
        $name = Str::nameRandom(8, true);
        $this->assertSame(8, strlen($name));
        $this->assertSame(strtolower($name), $name);

        $nameUpper = Str::nameRandom(5, false);
        $this->assertSame(5, strlen($nameUpper));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{5}$/', $nameUpper);
    }

    public function testHumanize(): void
    {
        $this->assertSame('Usuario Nome', Str::humanize('usuarioNome'));
        $this->assertSame('User Profile Picture', Str::humanize('user-profile-picture'));
        $this->assertSame('Data Nascimento', Str::humanize('dataNascimento'));
    }

    public function testNomeProprio(): void
    {
        // Capitalização padrão com conectivos em minúsculo
        $this->assertSame('João da Silva Pereira', Str::nomeProprio('joão da silva pereira'));

        // Conectivo como primeira palavra deve ser capitalizado
        $this->assertSame('De Paula Souza', Str::nomeProprio('de paula souza'));

        // Siglas fixas preservadas em maiúsculo
        $this->assertSame('Banco do Brasil BB', Str::nomeProprio('banco do brasil bb'));
        $this->assertSame('Empresa XP Investimentos', Str::nomeProprio('empresa xp investimentos'));

        // Numerais romanos
        $this->assertSame('Papa João Paulo II', Str::nomeProprio('papa joão paulo ii'));
        $this->assertSame('D. Pedro IV', Str::nomeProprio('d. pedro iv'));
        $this->assertSame('Século XXI', Str::nomeProprio('século xxi'));

        // Abreviações comerciais como S.A.
        $this->assertSame('Indústria e Comércio S.A.', Str::nomeProprio('indústria e comércio s.a.'));

        // Palavras adicionais customizadas em maiúsculo
        $this->assertSame('Instituto INSS Brasil', Str::nomeProprio('instituto inss brasil', ['INSS']));

        // Nulo ou vazio
        $this->assertSame('', Str::nomeProprio(null));
        $this->assertSame('', Str::nomeProprio(''));
    }

    public function testHasValue(): void
    {
        // Casos que retornam false
        $this->assertFalse(Str::hasValue(null));
        $this->assertFalse(Str::hasValue(''));

        // Diferença vital em relação ao empty() do PHP: '0' e 0 são considerados valores válidos!
        $this->assertTrue(Str::hasValue('0'));
        $this->assertTrue(Str::hasValue(0));
        $this->assertTrue(Str::hasValue(false));
        $this->assertTrue(Str::hasValue('texto'));
        $this->assertTrue(Str::hasValue([]));
    }
}
