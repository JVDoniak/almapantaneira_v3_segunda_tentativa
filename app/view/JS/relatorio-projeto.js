async function listarProjeto() {
    try {
        const resposta = await fetch('../../actions/relatorio-projeto.php');
        const projetos = await resposta.json();

        console.log(projetos);

        const bodyTable = document.getElementById("bodyTable");

        if(projetos.length === 0){
            bodyTable.innerHTML = "<p> Nenhum projeto encontrado. </p>";
            return;
        }

        projetos.forEach((projeto) => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${projeto.nome}</td>
            <td>${projeto.tipo_projeto}</td>
            <td>${new Date(projeto.data_inicio).toLocaleDateString("pt-BR")}</td>
            <td>${projeto.andamento} </td>
            <td>${projeto.responsavel}</td>
            <td>${projeto.local_projeto}</td>
            <td>${Number(projeto.valor_projeto).toLocaleString("pt-BR", {
                    style: "currency",
                    currency: "BRL"
                })}
            </td>
            <td>
                <div class="table-actions-cell">
                    <i class="bi bi-eye" title="Visualizar"></i>
                    <a href="edicao-projeto.php?id=${projeto.id_projeto}">
                        <i class="bi bi-pencil-square" title="Editar"></i>
                    </a>
                    <label class="switch-component">
                        <input type="checkbox" ${projeto.ativo ? "checked" : ""}>
                        <span class="switch-slider"></span>
                    </label>
                </div>
            </td>
        `;

        bodyTable.appendChild(tr);
    });

    } catch (error) {
         console.error("Erro ao carregar lista", error);
         document.getElementById("bodyTable").innerHTML = "<p>Não foi possível carregar o relatório de projetos.</p>";
    }
}

document.addEventListener("DOMContentLoaded", listarProjeto);