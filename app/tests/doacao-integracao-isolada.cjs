// Integração HTTP/PDO em cópia temporária com SQLite e dados fictícios.
// --browser acrescenta Chrome headless via CDP, sem instalar dependências.
// Nunca usa o banco configurado na aplicação original nem altera seus arquivos.
const fs = require('fs');
const os = require('os');
const path = require('path');
const net = require('net');
const cp = require('child_process');
const assert = require('assert/strict');
const { once } = require('events');
const repo = path.resolve(__dirname, '..');
const php = process.env.ALMA_TEST_PHP || 'C:/xampp/php/php.exe';
const browserPath = process.env.ALMA_TEST_BROWSER || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const browserEnabled = process.argv.includes('--browser');
const copyEnabled = process.argv.includes('--er03');
const requirementsEnabled = process.argv.includes('--requisitos');
const results = [];
const root = fs.mkdtempSync(path.join(os.tmpdir(), 'alma-integracao-'));
const site = path.join(root, 'site');
const dbFile = path.join(root, 'ficticio.sqlite');
let server, browser, cdp;
let phpLog = '';
let browserLog = '';
const delay = ms => new Promise(resolve => setTimeout(resolve, ms));
const test = (name, details = {}) => { results.push({ name, ...details }); console.log('OK: ' + name); };
async function port() {
    const s = net.createServer(); s.listen(0, '127.0.0.1'); await once(s, 'listening');
    const value = s.address().port; await new Promise(resolve => s.close(resolve)); return value;
}
async function waitFor(fn, label) {
    const until = Date.now() + 15000;
    while (Date.now() < until) {
        try { const result = await fn(); if (result) return result; } catch {}
        await delay(100);
    }
    throw Error('Tempo esgotado: ' + label);
}
function copySources(src, dest) {
    fs.mkdirSync(dest, { recursive: true });
    for (const entry of fs.readdirSync(src, { withFileTypes: true })) {
        if (['uploads', 'vendor', 'node_modules'].includes(entry.name)) continue;
        const from = path.join(src, entry.name), to = path.join(dest, entry.name);
        if (entry.isDirectory()) copySources(from, to);
        else if (/\.(php|js|css|png|jpg|svg|ico)$/i.test(entry.name)) fs.copyFileSync(from, to);
    }
}
function provision() {
    for (const dir of ['app', 'public']) copySources(path.join(repo, dir), path.join(site, dir));
    const database = path.join(site, 'app/model/Database.php');
    const source = fs.readFileSync(database, 'utf8');
    const original = 'new PDO("mysql:host=" . $this->local . ";dbname=" . $this->db, $this->user, $this->password)';
    assert(source.includes(original), 'Conector inesperado: não iniciar testes');
    let isolated = source.replace(original, "new PDO('sqlite:' . getenv('ALMA_TEST_SQLITE'))")
        .replace('$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);',
            "$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n            $this->conn->exec('PRAGMA foreign_keys = ON');");
    // Compatibilidade de dialeto somente na copia SQLite de testes.
    isolated = isolated.replace("$this->conn->exec('PRAGMA foreign_keys = ON');", "$this->conn->exec('PRAGMA foreign_keys = ON');\n            $this->conn->sqliteCreateFunction('LAST_INSERT_ID', fn() => (int) $this->conn->lastInsertId());");
    assert(!isolated.includes('new PDO("mysql:'), 'Conexão MySQL não foi removida da cópia');
    fs.writeFileSync(database, isolated);
    // Autenticação é fixture apenas na cópia de testes, nunca no código original.
    fs.writeFileSync(path.join(site, 'public/includes/autenticacao.php'),
        "<?php session_start(); $_SESSION['usuario_id'] = 1;");
    const setup = path.join(root, 'setup.php');
    fs.writeFileSync(setup, `<?php
$db = new PDO('sqlite:' . getenv('ALMA_TEST_SQLITE'));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('PRAGMA foreign_keys = ON');
$db->exec("CREATE TABLE endereco (id_endereco INTEGER PRIMARY KEY AUTOINCREMENT, cep TEXT NOT NULL, logradouro TEXT NOT NULL, numero TEXT NOT NULL, complemento TEXT NOT NULL, bairro TEXT, cidade TEXT, estado TEXT);
CREATE TABLE doador (id_doador INTEGER PRIMARY KEY, nome TEXT NOT NULL, status_doador INTEGER DEFAULT 1, id_endereco INTEGER, tipo TEXT, cpf_cnpj TEXT, telefone TEXT, email TEXT, razao_social TEXT, contato_empresa TEXT, FOREIGN KEY(id_endereco) REFERENCES endereco(id_endereco));
CREATE TABLE projeto (id_projeto INTEGER PRIMARY KEY, nome TEXT NOT NULL, status_projeto INTEGER DEFAULT 1, responsavel TEXT, local_projeto TEXT, tipo_projeto TEXT, valor_projeto REAL, data_inicio TEXT, data_termino TEXT, andamento TEXT, descricao TEXT);
CREATE TABLE tipo_doacao (id_tipo_doacao INTEGER PRIMARY KEY, descricao TEXT NOT NULL);
CREATE TABLE doacao (
 id_doacao INTEGER PRIMARY KEY AUTOINCREMENT, id_doador INTEGER,
 tipo TEXT NOT NULL, id_projeto INTEGER DEFAULT NULL, descricao TEXT,
 valor DECIMAL(10,2) NOT NULL, forma_pagamento VARCHAR(50) NOT NULL,
 data_doacao DATE NOT NULL, data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
 status_doacao INTEGER DEFAULT 1,
 FOREIGN KEY(id_doador) REFERENCES doador(id_doador),
 FOREIGN KEY(id_projeto) REFERENCES projeto(id_projeto));
INSERT INTO endereco VALUES (1,'79000000','Rua de teste',10,'','Centro','Campo Grande','Mato Grosso do Sul');
INSERT INTO doador (id_doador,nome,status_doador,id_endereco,tipo,cpf_cnpj,telefone,email) VALUES (3, 'Doador ficticio de integracao', 1,1,'Pessoa Física','00000000000','6700000000','teste@example.invalid');
INSERT INTO projeto (id_projeto,nome) VALUES (7, 'Projeto ficticio de integracao');
INSERT INTO tipo_doacao VALUES (1, 'Financeira');");
if (getenv('ALMA_TEST_COPY') === '1') {
 $db->exec("CREATE TABLE tipo_despesa (id_tipo_despesa INTEGER PRIMARY KEY, descricao TEXT NOT NULL);
 CREATE TABLE categoria_despesa (id_categoria_despesa INTEGER PRIMARY KEY, descricao TEXT NOT NULL);
 CREATE TABLE fornecedor (id_fornecedor INTEGER PRIMARY KEY, nome TEXT, status_fornecedor INTEGER DEFAULT 1, razao_social TEXT, cpf_cnpj TEXT, telefone TEXT, email TEXT, contato TEXT, id_endereco INTEGER REFERENCES endereco(id_endereco));
 CREATE TABLE despesa (id_despesa INTEGER PRIMARY KEY, nome TEXT, id_categoria_despesa INTEGER REFERENCES categoria_despesa(id_categoria_despesa), id_tipo_despesa INTEGER REFERENCES tipo_despesa(id_tipo_despesa), id_fornecedor INTEGER REFERENCES fornecedor(id_fornecedor), id_projeto INTEGER REFERENCES projeto(id_projeto), valor REAL, data_vencimento TEXT, descricao TEXT, forma_pagamento TEXT, anexo_nota TEXT, data_lancamento TEXT DEFAULT CURRENT_TIMESTAMP, status_despesa INTEGER DEFAULT 1);
 INSERT INTO tipo_despesa VALUES (1,'Fixa'),(2,'Variável');
 INSERT INTO categoria_despesa VALUES (37,'Medicamentos');");
}
`);
    cp.execFileSync(php, [setup], { env: { ...process.env, ALMA_TEST_SQLITE: dbFile, ALMA_CONFIG_FILE: '', DB_HOST: 'isolado.invalid', DB_NAME: 'ficticio', DB_USER: 'teste', DB_PASS: 'ficticio', ALMA_TEST_COPY: copyEnabled ? '1' : '0' } });
    const router = path.join(root, 'router.php');
    fs.writeFileSync(router, `<?php
$root = realpath(__DIR__ . '/site');
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri === '/__test/state' || $uri === '/__test/doadores' || $uri === '/__test/despesas') {
 header('Content-Type: application/json');
 $db = new PDO('sqlite:' . getenv('ALMA_TEST_SQLITE'));
 $query = $uri === '/__test/despesas' ? 'SELECT * FROM despesa ORDER BY id_despesa' : ($uri === '/__test/state' ? 'SELECT * FROM doacao ORDER BY id_doacao'
   : 'SELECT d.*, e.cep, e.logradouro, e.numero, e.bairro, e.cidade, e.estado FROM doador d LEFT JOIN endereco e ON e.id_endereco=d.id_endereco ORDER BY d.id_doador');
 echo json_encode($db->query($query)->fetchAll(PDO::FETCH_ASSOC));
 return;
}
$file = realpath($root . rawurldecode($uri));
if (!$file || !str_starts_with(strtolower($file), strtolower($root . DIRECTORY_SEPARATOR))) {
 http_response_code(404); return;
}
if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'php') {
 chdir(dirname($file)); require $file; return;
}
return false;
`);
    return router;
}
class CDP {
    constructor(ws) {
        this.ws = ws; this.next = 1; this.pending = new Map(); this.events = [];
        ws.addEventListener('message', ({ data }) => {
            const msg = JSON.parse(data);
            if (msg.id) {
                const item = this.pending.get(msg.id);
                if (!item) return;
                this.pending.delete(msg.id); clearTimeout(item.timer);
                if (msg.error) item.reject(Error(JSON.stringify(msg.error))); else item.resolve(msg.result);
            } else {
                this.events.push(msg);
                if (msg.method === 'Page.javascriptDialogOpening') {
                    this.send('Page.handleJavaScriptDialog', { accept: true }, msg.sessionId).catch(() => {});
                }
                if (msg.method === 'Fetch.requestPaused' && this.mockCnpj) {
                    const cnpj = new URL(msg.params.request.url).pathname.slice(1);
                    const status = this.mockCnpjStatus || 200;
                    const payload = status === 200 ? { cnpj, razao_social: 'Empresa ficticia LTDA', nome_fantasia: 'Empresa ficticia',
                        tipo_logradouro: 'Rua', logradouro: 'Teste', numero: '10', bairro: 'Centro', cep: '79000000', uf: 'MS',
                        municipio: 'Campo Grande', email: 'empresa@example.invalid', telefones: [{ ddd: '67', numero: '999999999', is_fax: false }] } : { error: 'simulado' };
                    this.send('Fetch.fulfillRequest', { requestId: msg.params.requestId, responseCode: status,
                        responseHeaders: [{ name: 'Content-Type', value: 'application/json' }, { name: 'Access-Control-Allow-Origin', value: '*' }],
                        body: Buffer.from(JSON.stringify(payload)).toString('base64') }, msg.sessionId).catch(() => {});
                }
            }
        });
    }
    send(method, params = {}, sessionId) {
        const id = this.next++;
        return new Promise((resolve, reject) => {
            const timer = setTimeout(() => { this.pending.delete(id); reject(Error('CDP timeout: ' + method)); }, 15000);
            this.pending.set(id, { resolve, reject, timer });
            this.ws.send(JSON.stringify({ id, method, params, ...(sessionId ? { sessionId } : {}) }));
        });
    }
    async evaluate(expression) {
        const data = await this.send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true }, this.session);
        if (data.exceptionDetails) throw Error(JSON.stringify(data.exceptionDetails));
        return data.result.value;
    }
    async navigate(url, ready) {
        const previousOrigin = await this.evaluate('performance.timeOrigin');
        await this.send('Page.navigate', { url }, this.session);
        await waitFor(() => this.evaluate('performance.timeOrigin !== ' + previousOrigin + ' && document.readyState === "complete" && (' + ready + ')'), url);
    }
    async click(selector) {
        const box = await this.evaluate(`(() => {const e=document.querySelector(${JSON.stringify(selector)}); e.scrollIntoView({block:'center',behavior:'instant'}); const r=e.getBoundingClientRect();const x=r.x+r.width/2,y=r.y+r.height/2;const hit=document.elementFromPoint(x,y);return {x,y,target:hit===e||e.contains(hit)};})()`);
        assert(box.target, 'Alvo de clique encoberto: ' + selector);
        delete box.target;
        await this.send('Input.dispatchMouseEvent', { type: 'mousePressed', ...box, button: 'left', clickCount: 1 }, this.session);
        await this.send('Input.dispatchMouseEvent', { type: 'mouseReleased', ...box, button: 'left', clickCount: 1 }, this.session);
    }
    async fill(selector, value) {
        await this.evaluate(`(() => { const e=document.querySelector(${JSON.stringify(selector)}); e.value=${JSON.stringify(value)}; e.dispatchEvent(new Event('input',{bubbles:true})); e.dispatchEvent(new Event('change',{bubbles:true})); })()`);
    }
    async dialogAfter(fn, expected) {
        const start = this.events.length;
        await fn();
        let dialog;
        try {
            dialog = await waitFor(() => this.events.slice(start).find(e => e.method === 'Page.javascriptDialogOpening'), 'alerta do formulário');
        } catch (error) {
            const diagnostic = await this.evaluate('Array.from(document.querySelectorAll("input,select,button")).map(e=>({id:e.id,value:e.value,valid:e.validity.valid,message:e.validationMessage,form:e.form?.id}))');
            fs.writeFileSync(path.join(root, 'form-diagnostic.json'), JSON.stringify(diagnostic, null, 2));
            const shot = await this.send('Page.captureScreenshot', { format: 'png' }, this.session);
            fs.writeFileSync(path.join(root, 'falha.png'), Buffer.from(shot.data, 'base64'));
            throw error;
        }
        assert(dialog.params.message.includes(expected), dialog.params.message);
    }
}
async function launchBrowser(base, state) {
    assert(fs.existsSync(browserPath), 'Chrome não encontrado; não instalar automaticamente');
    const debug = await port(); let output = '';
    browser = cp.spawn(browserPath, ['--headless=new', '--no-first-run', '--no-default-browser-check',
        '--disable-background-networking', '--disable-sync', '--disable-extensions', '--disable-gpu',
        '--remote-debugging-port=' + debug, '--user-data-dir=' + path.join(root, 'chrome-profile'),
        '--window-size=1280,900', 'about:blank'], { windowsHide: true, stdio: ['ignore', 'ignore', 'pipe'] });
    browser.on('error', error => { output += error.message; browserLog += error.message; });
    browser.stderr.on('data', chunk => { output += chunk.toString(); browserLog += chunk.toString(); });
    const version = await waitFor(async () => {
        if (browser.exitCode !== null) throw Error('Chrome encerrou: ' + output.slice(-1000));
        const response = await fetch('http://127.0.0.1:' + debug + '/json/version'); return response.json();
    }, 'Chrome headless: ' + output.slice(-400));
    const ws = new WebSocket(version.webSocketDebuggerUrl); await once(ws, 'open'); cdp = new CDP(ws);
    const target = await cdp.send('Target.createTarget', { url: 'about:blank' });
    const attached = await cdp.send('Target.attachToTarget', { targetId: target.targetId, flatten: true }); cdp.session = attached.sessionId;
    for (const method of ['Page.enable', 'Runtime.enable', 'Network.enable']) await cdp.send(method, {}, cdp.session);
    await cdp.send('Network.setBlockedURLs', { urls: ['*://cdn.jsdelivr.net/*'] }, cdp.session);
    test('Chrome headless disponível sem instalação', { version: version.Browser });
    async function form() {
        await cdp.navigate(base + '/app/view/pages/cadastro-doacao.php', '!!document.querySelector("#nome_doador")');
        await waitFor(() => cdp.evaluate('performance.getEntriesByType("resource").some(r=>r.name.includes("buscar-doador-input.php"))'), 'opções de doadores carregadas');
        await cdp.click('#nome_doador');
        await cdp.fill('#nome_doador', 'Doador ficticio de integracao');
        await waitFor(() => cdp.evaluate('document.querySelectorAll("#sugestoes_doadores > div").length === 1'), 'autocomplete');
        await cdp.click('#sugestoes_doadores > div');
        assert.equal(await cdp.evaluate('document.querySelector("#id_doador").value'), '3');
        await cdp.fill('#tipo', '1'); await cdp.fill('#valor', '1200.00');
        await cdp.fill('#forma_pagamento', 'Transferência Bancária'); await cdp.fill('#data_doacao', '2026-11-02');
    }
    for (const projeto of ['', '7']) {
        await form(); await cdp.fill('#projeto', projeto);
        if (copyEnabled) {
            assert.equal(await cdp.evaluate('document.querySelector(".content-header p").textContent.trim()'), 'Gerenciar e Cadastrar Doações');
            assert.equal(await cdp.evaluate('document.title'), 'Cadastro de Doações - Alma Pantaneira');
            const payload = await cdp.evaluate('(() => {const f=new FormData(document.querySelector("#formCadastro"));return ["id_doador","tipo","id_projeto","valor","forma_pagamento","data_doacao"].map(k=>[k,f.get(k)]);})()');
            assert.deepEqual(payload, [['id_doador', '3'], ['tipo', '1'], ['id_projeto', projeto], ['valor', '1200.00'],
                ['forma_pagamento', 'Transferência Bancária'], ['data_doacao', '2026-11-02']]);
            test('ER-03: títulos e payload de doação preservados ' + (projeto ? 'com projeto' : 'sem projeto'));
        }
        await cdp.dialogAfter(() => cdp.click('#salvar'), 'Doação cadastrada com sucesso');
        const rows = await state(), last = rows.at(-1);
        assert.equal(last.id_projeto, projeto ? 7 : null); assert.equal(last.id_doador, 3);
        assert.equal(Number(last.valor), 1200);
        test('Navegador: cadastro ' + (projeto ? 'com projeto' : 'sem projeto'), { database: 'SQLite isolado', id: last.id_doacao });
    }
    await form(); await cdp.fill('#nome_doador', 'Doador inexistente');
    const before = (await state()).length;
    await cdp.dialogAfter(() => cdp.click('#salvar'), 'Selecione um doador');
    assert.equal((await state()).length, before); test('Navegador: texto sem doador selecionado bloqueado');
    await form(); await cdp.fill('#id_doador', '999999');
    await cdp.dialogAfter(() => cdp.click('#salvar'), 'Selecione um doador válido');
    assert.equal((await state()).length, before); test('Navegador: ID de doador inexistente rejeitado no backend');
    for (const field of ['#tipo', '#forma_pagamento', '#data_doacao']) {
        await form(); await cdp.fill(field, '');
        assert.equal(await cdp.evaluate('document.querySelector("#formCadastro").checkValidity()'), false);
        await cdp.click('#salvar'); await delay(150);
        assert.equal((await state()).length, before); test('Navegador: obrigatoriedade nativa de ' + field);
    }
    if (copyEnabled) {
        await form();
        const defaultDoador = await cdp.evaluate('document.querySelector("#id_doador").defaultValue');
        await cdp.click('#formCadastro button[type="reset"]');
        assert.equal(await cdp.evaluate('document.querySelector("#nome_doador").value'), '');
        assert.equal(await cdp.evaluate('document.querySelector("#id_doador").value'), defaultDoador);
        assert.equal((await state()).length, before);
        test('ER-03: Limpar formulário preserva reset nativo, sem gravação');
        await cdp.click('#btnAdicionarTipo');
        assert.equal(await cdp.evaluate('document.querySelector("#modalTipoDoacao").classList.contains("oculta")'), false);
        await cdp.click('#btnCancelarTipo');
        assert.equal(await cdp.evaluate('document.querySelector("#modalTipoDoacao").classList.contains("oculta")'), true);
        test('ER-03: modal de tipo abre/fecha com os mesmos identificadores');
        await cdp.navigate(base + '/app/view/pages/cadastro-despesa.php', '!!document.querySelector("#formCadastroDespesa")');
        assert.equal(await cdp.evaluate('document.querySelector(".content-header p").textContent.trim()'), 'Gerenciar e Cadastrar Despesas');
        assert.equal(await cdp.evaluate('document.title'), 'Cadastro de Despesas - Alma Pantaneira');
        assert.deepEqual(await cdp.evaluate('[document.querySelector("#id_categoria_despesa").name,document.querySelector("#id_tipo_despesa").name,document.querySelector("#id_categoria_despesa option:last-child").value]'),
            ['id_categoria_despesa', 'id_tipo_despesa', '37']);
        assert.equal(await cdp.evaluate('document.querySelector("#fornecedor").required || document.querySelector("#projeto").required'), false);
        test('ER-03: cadastro de despesas com plural autorizado e campos ER-01/ER-02 preservados');
        await cdp.fill('#nome', 'Despesa ficticia preservada nos modais');
        await cdp.fill('#valor', '75.00');
        await cdp.click('#btnAdicionarProjeto');
        await waitFor(() => cdp.evaluate('!!document.querySelector("#formCadastroProjeto")'), 'modal projeto carregado');
        await cdp.evaluate('document.querySelector("#formCadastroProjeto").requestSubmit()');
        assert.equal(await cdp.evaluate('document.querySelector("#modal").hidden'), false);
        await cdp.fill('#nomeProjeto','Projeto ficticio criado pelo modal');
        for (const [name,value] of Object.entries({responsavel:'Responsavel ficticio',local_projeto:'Local ficticio',valor_projeto:'1500',data_inicio:'2026-11-01',data_termino:'2026-12-01',descricao:'Projeto de teste isolado'}))
            await cdp.fill('#formCadastroProjeto [name="'+name+'"]',value);
        await cdp.evaluate('for (const s of document.querySelectorAll("#formCadastroProjeto select")) s.value=Array.from(s.options).find(o=>o.value).value');
        await cdp.evaluate('document.querySelector("#formCadastroProjeto").requestSubmit()');
        await waitFor(() => cdp.evaluate('document.querySelector("#modal").hidden && Number(document.querySelector("#id_projeto").value)>7'), 'projeto cadastrado e selecionado');
        assert.equal(await cdp.evaluate('document.querySelector("#nome").value'),'Despesa ficticia preservada nos modais');
        test('V3 modal projeto: formulario completo, obrigatorios, gravacao SQLite e selecao sem perder despesa');
        await cdp.click('#btnNovoFornecedor');
        await waitFor(() => cdp.evaluate('!!document.querySelector("#formCadastroFornecedor")'), 'modal fornecedor carregado');
        cdp.mockCnpj = true;
        await cdp.send('Fetch.enable',{patterns:[{urlPattern:'https://api.opencnpj.org/*',requestStage:'Request'}]},cdp.session);
        await cdp.click('#modal_fornecedor_cpf_cnpj'); await cdp.fill('#modal_fornecedor_cpf_cnpj','11222333000181'); await cdp.click('#modal_fornecedor_razao_social');
        await waitFor(() => cdp.evaluate('document.querySelector("#modal_fornecedor_razao_social").value === "Empresa ficticia LTDA"'),'CNPJ no modal');
        await cdp.fill('#modal_fornecedor_contato','Contato ficticio'); await cdp.fill('#modal_fornecedor_numero','S/N');
        await cdp.evaluate('document.querySelector("#formCadastroFornecedor").requestSubmit()');
        await waitFor(() => cdp.evaluate('document.querySelector("#modal").hidden && Number(document.querySelector("#id_fornecedor").value)>0'),'fornecedor cadastrado e selecionado');
        assert.equal(await cdp.evaluate('document.querySelector("#nome").value'),'Despesa ficticia preservada nos modais');
        assert.equal(await cdp.evaluate('document.querySelector("#valor").value'),'75.00');
        test('V3 modal fornecedor: CNPJ mock, endereco S/N, gravacao SQLite e selecao sem sobrescrever despesa');
        await cdp.click('#btnAdicionarCategoria');
        await cdp.fill('#descricaoCategoria','Categoria ficticia criada no modal');
        await cdp.dialogAfter(()=>cdp.click('#formCadastroCategoria button[type="submit"]'),'Cadastrado com sucesso');
        await waitFor(()=>cdp.evaluate('Number(document.querySelector("#id_categoria_despesa").value)>37'),'categoria criada e selecionada');
        test('V3 modal categoria: cadastra e seleciona sem recarregar despesa');
        await cdp.fill('#data_vencimento','2026-11-30');
        await cdp.evaluate('document.querySelector("#forma_pagamento").value="Pix";document.querySelector("#id_tipo_despesa").value="1"');
        await cdp.dialogAfter(()=>cdp.click('#formCadastroDespesa button[type="submit"]'),'Despesa cadastrada com sucesso');
        const despesasIsoladas=await (await fetch(base+'/__test/despesas')).json();
        assert.equal(despesasIsoladas.length,1);
        assert.equal(despesasIsoladas[0].nome,'Despesa ficticia preservada nos modais');
        assert(despesasIsoladas[0].id_projeto>7 && despesasIsoladas[0].id_fornecedor>0 && despesasIsoladas[0].id_categoria_despesa>37);
        assert.equal(despesasIsoladas[0].id_tipo_despesa,1);
        test('V3 navegador/PDO: despesa salva com registros dos tres modais e tipo separado');

    }
    if (requirementsEnabled) {
        await cdp.navigate(base + '/app/view/pages/cadastro-doador.php', '!!document.querySelector("#cpf_cnpj")');
        await cdp.evaluate('selecionarTipoDoador("Pessoa Física")');
        assert.equal(await cdp.evaluate('document.querySelector("#estado").required'), false);
        await cdp.fill('#cpf_cnpj', '52998224725'); await cdp.fill('#nome', 'Doador ficticio sem endereco');
        await cdp.fill('#telefone', '67999999999'); await cdp.fill('#email', 'doador@example.invalid');
        await cdp.dialogAfter(() => cdp.click('#btnSalvar'), 'Doador cadastrado com sucesso');
        const doadores = await (await fetch(base + '/__test/doadores')).json();
        assert.equal(doadores.at(-1).id_endereco, null);
        test('Requisito 6 navegador: cadastra doador sem endereco e sem dependencia do IBGE');
        cdp.mockCnpj = true;
        await cdp.send('Fetch.enable', { patterns: [{ urlPattern: 'https://api.opencnpj.org/*', requestStage: 'Request' }] }, cdp.session);
        await cdp.navigate(base + '/app/view/pages/cadastro-fornecedor.php', '!!document.querySelector("#cpf_cnpj")');
        await cdp.click('#cpf_cnpj'); await cdp.fill('#cpf_cnpj', '11222333000181'); await cdp.click('#razao_social');
        await waitFor(() => cdp.evaluate('document.querySelector("#razao_social").value === "Empresa ficticia LTDA"'), 'CNPJ preenchido');
        assert.equal(await cdp.evaluate('document.querySelector("#numero").value'), '10');
        assert.equal(await cdp.evaluate('document.querySelector("#estado").value'), 'Mato Grosso do Sul');
        test('Requisito 7 navegador: CNPJ mock preenche campos compativeis e mascaras');
        await cdp.fill('#razao_social', 'Nome corrigido manualmente');
        cdp.mockCnpjStatus = 404;
        await cdp.click('#cpf_cnpj'); await cdp.fill('#cpf_cnpj', '00000000000191'); await cdp.click('#razao_social');
        await waitFor(() => cdp.evaluate('document.querySelector("#cpf_cnpj + p").textContent.includes("não encontrado")'), 'CNPJ 404');
        assert.equal(await cdp.evaluate('document.querySelector("#razao_social").value'), 'Nome corrigido manualmente');
        test('Requisito 7 navegador: CNPJ nao encontrado preserva edicao manual');
        cdp.mockCnpjStatus = 503;
        await cdp.click('#cpf_cnpj'); await cdp.fill('#cpf_cnpj', '11222333000181'); await cdp.click('#razao_social');
        await waitFor(() => cdp.evaluate('document.querySelector("#cpf_cnpj + p").textContent.includes("indisponível")'), 'CNPJ 503');
        assert.equal(await cdp.evaluate('document.querySelector("#formCadastroFornecedor button[type=submit]").disabled'), false);
        assert.equal(await cdp.evaluate('document.querySelector("#cpf_cnpj").value.replace(/\\D/g, "")'), '11222333000181');
        test('Requisito 7 navegador: API indisponivel nao desabilita cadastro nem apaga CNPJ');
        await cdp.send('Fetch.disable', {}, cdp.session);
    }
    await cdp.navigate(base + '/app/view/pages/listagem-doacao.php', 'document.querySelectorAll("#bodyTable tr").length > 0');
    const displayed = await cdp.evaluate('Array.from(document.querySelectorAll("#bodyTable tr")).map(r=>Array.from(r.cells).map(c=>c.textContent.trim()))');
    assert(displayed.some(r => r[3] === 'Sem projeto')); assert(displayed.some(r => r[3] === 'Projeto ficticio de integracao'));
    assert.equal(displayed.length, (await state()).length);
    test('Navegador: listagem real HTTP mostra Sem projeto e projeto vinculado');
    const errors = cdp.events.filter(e => e.method === 'Runtime.exceptionThrown');
    assert.deepEqual(errors, [], 'Exceções JavaScript no navegador');
    const screenshot = await cdp.send('Page.captureScreenshot', { format: 'png' }, cdp.session);
    fs.writeFileSync(path.join(root, 'listagem.png'), Buffer.from(screenshot.data, 'base64'));
    test('Navegador: nenhuma exceção JavaScript; screenshot salvo');
}
(async () => {
    const router = provision(); const httpPort = await port(); const base = 'http://127.0.0.1:' + httpPort;
    server = cp.spawn(php, ['-d', 'session.save_path=' + root, '-S', '127.0.0.1:' + httpPort, '-t', site, router], {
        windowsHide: true, env: { ...process.env, ALMA_TEST_SQLITE: dbFile, ALMA_CONFIG_FILE: '', DB_HOST: 'isolado.invalid', DB_NAME: 'ficticio', DB_USER: 'teste', DB_PASS: 'ficticio' }, stdio: ['ignore', 'pipe', 'pipe']
    });
    server.on('error', error => { phpLog += error.message; });
    server.stderr.on('data', chunk => { phpLog += chunk.toString(); });
    const state = async () => (await fetch(base + '/__test/state')).json();
    await waitFor(async () => Array.isArray(await state()), 'servidor PHP isolado');
    const post = async (action, fields) => {
        const response = await fetch(base + '/app/actions/' + action + '-doacao.php', { method: 'POST', body: new URLSearchParams(fields) });
        const text = await response.text(); assert.equal(response.status, 200, text); return JSON.parse(text);
    };
    const data = { id_doador: '3', tipo: '1', descricao: 'Teste isolado', valor: '1200.00', forma_pagamento: 'Transferência Bancária', data_doacao: '2026-11-02' };
    for (const projeto of ['', '7', undefined]) {
        const payload = { ...data }; if (projeto !== undefined) payload.id_projeto = projeto;
        assert.equal((await post('cadastro', payload)).success, true);
        const row = (await state()).at(-1); assert.equal(row.id_projeto, projeto === '7' ? 7 : null);
        assert.equal(Number(row.valor), 1200); assert.equal(row.forma_pagamento, data.forma_pagamento);
        test('HTTP/PDO: cadastro projeto ' + (projeto === undefined ? 'ausente' : projeto || 'vazio'), { database: 'SQLite isolado' });
    }
    const before = (await state()).length;
    for (const id of ['0', '999999']) {
        const response = await post('cadastro', { ...data, id_doador: id });
        assert.equal(response.success, false); assert(response.message.includes('doador')); assert.equal((await state()).length, before);
        test('HTTP/PDO: doador ' + id + ' rejeitado sem inserir');
    }
    for (const field of ['valor', 'forma_pagamento']) {
        const response = await post('cadastro', { ...data, [field]: '' });
        assert.equal(response.success, false); assert.equal((await state()).length, before); test('HTTP/PDO: obrigatório ' + field);
    }
    const failure = await post('cadastro', { ...data, id_projeto: '999999' });
    assert.equal(failure.success, false); assert.equal((await state()).length, before);
    assert(phpLog.includes('SQLSTATE=23000'), 'Falha de FK deve ser registrada'); test('HTTP/PDO: FK de projeto inexistente e erro de inserção registrado');
    const list = await (await fetch(base + '/app/actions/listagem-doacao.php')).json();
    assert.equal(list.length, before); assert(list.some(r => r.projeto === null)); assert(list.some(r => r.projeto === 'Projeto ficticio de integracao'));
    test('HTTP/PDO: LEFT JOIN preserva doações sem projeto no JSON');
    for (const projeto of ['7', '']) {
        const response = await post('editar', { id_doacao: '1', id_doador: '3', tipo: '1', descricao: 'Editada', forma: data.forma_pagamento,
            data_doacao: data.data_doacao, id_projeto: projeto });
        assert.equal(response.status, 200); const row = (await state())[0];
        assert.equal(row.id_projeto, projeto ? 7 : null); assert.equal(Number(row.valor), 1200);
        test('HTTP/PDO: edição ' + (projeto ? 'vincula' : 'remove') + ' projeto e preserva valor');
    }
    if (requirementsEnabled) {
        const donors = async () => (await fetch(base + '/__test/doadores')).json();
        const donorData = { nome: 'Doador ficticio limiar', tipo: 'Pessoa Física', cpf_cnpj: '00000000001',
            telefone: '67999999999', email: 'limiar@example.invalid' };
        assert.equal((await post('cadastro', data)).success, true); // Doador 3 já possui endereço.
        const response = await fetch(base + '/app/actions/cadastro-doador.php', { method: 'POST', body: new URLSearchParams(donorData) });
        assert.equal((await response.json()).success, true);
        const donor = (await donors()).at(-1); assert.equal(donor.id_endereco, null);
        test('Requisito 6 HTTP: doador sem endereco persiste FK NULL');
        for (const valor of ['999.99', '700.00']) {
            assert.equal((await post('cadastro', { ...data, id_doador: String(donor.id_doador), valor })).success, true);
        }
        test('Requisito 6 HTTP: varias doacoes menores nao sao somadas para exigir endereco');
        const count = (await state()).length;
        for (const valor of ['1000.00', '1200.00']) {
            const result = await post('cadastro', { ...data, id_doador: String(donor.id_doador), valor });
            assert.equal(result.success, false); assert(result.message.includes('endereço'));
        }
        assert.equal((await state()).length, count);
        test('Requisito 6 HTTP: limite inclusivo de 1000 e acima bloqueados sem endereco');
        const lower = (await state()).at(-1);
        const edit = await post('editar', { id_doacao: String(lower.id_doacao), id_doador: String(donor.id_doador),
            tipo: '1', forma: data.forma_pagamento, data_doacao: data.data_doacao, valor: '1000.00', id_projeto: '' });
        assert.equal(edit.status, 400); test('Requisito 6 HTTP: edicao nao contorna o limiar');
        const endereco = { cep: '79000000', logradouro: 'Rua teste', numero: '10', complemento: '', bairro: 'Centro', cidade: 'Campo Grande', estado: 'Mato Grosso do Sul' };
        const editDonor = async fields => (await fetch(base + '/app/actions/editar-doador.php', { method: 'POST', body: new URLSearchParams({ ...donorData, id_doador: String(donor.id_doador), ...fields }) })).json();
        assert.equal((await editDonor({})).status, 200);
        assert.equal((await editDonor(endereco)).status, 200);
        assert((await donors()).at(-1).id_endereco > 0);
        assert.equal((await post('cadastro', { ...data, id_doador: String(donor.id_doador), valor: '1000.00' })).success, true);
        test('Requisito 6 HTTP: edita doador sem endereco, adiciona endereco e libera doacao de 1000');
        assert.equal((await editDonor({})).status, 400);
        assert((await donors()).at(-1).id_endereco > 0);
        test('Requisito 6 HTTP: endereco nao removido enquanto existir doacao individual alta');
        const partial = await fetch(base + '/app/actions/cadastro-doador.php', { method: 'POST', body: new URLSearchParams({ ...donorData, cpf_cnpj: '00000000002', cep: '79000000' }) });
        assert.equal((await partial.json()).success, false);
        for (const numero of ['25', '1500', 'S/N']) {
            assert.equal((await editDonor({ ...endereco, numero })).status, 200);
            assert.equal((await donors()).at(-1).numero, numero);
        }
        const incompatible = await editDonor({ ...endereco, numero: '1'.repeat(21) });
        assert.equal(incompatible.status, 400);
        test('Requisito 6 HTTP: 25/1500/SN preservados; endereco parcial e numero acima de 20 rejeitados');
    }
    if (browserEnabled) await launchBrowser(base, state);
    console.log('APROVADO: ' + results.length + ' verificações; artefatos temporários: ' + root);
})().catch(error => {
    console.error(error.stack); process.exitCode = 1;
}).finally(async () => {
    fs.writeFileSync(path.join(root, 'php-server.log'), phpLog);
    fs.writeFileSync(path.join(root, 'chrome.log'), browserLog);
    if (cdp) fs.writeFileSync(path.join(root, 'cdp-events.json'), JSON.stringify(cdp.events, null, 2));
    fs.writeFileSync(path.join(root, 'resultados.json'), JSON.stringify({ results, failed: !!process.exitCode, isolated: true, browserEnabled }, null, 2));
    if (cdp) { try { await cdp.send('Browser.close'); } catch {} cdp.ws.close(); }
    if (browser && browser.exitCode === null) browser.kill();
    if (server) server.kill();
    console.log('Fim do teste; nenhum banco real utilizado. Artefatos: ' + root);
});
