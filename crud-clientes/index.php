<?php require_once "config.php"; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>CRUD Clientes (AJAX + API C#)</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
  <div class="topbar">
    <h1>CRUD Clientes</h1>
    <button class="btn primary" type="button" onclick="openCreateModal()">+ Nuevo cliente</button>
  </div>

  <div id="alert" class="alert" style="display:none;"></div>

  <div class="card">
    <h2>Listado</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Cédula</th><th>Nombres</th><th>Email</th><th>Teléfono</th><th>Estado</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tbodyClientes">
          <!-- JS render -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL CREAR -->
<div id="createBackdrop" class="modal-backdrop" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="createTitle">
    <div class="modal-header">
      <h3 id="createTitle">Nuevo Cliente</h3>
      <button class="icon-btn" type="button" onclick="closeCreateModal()">✕</button>
    </div>

    <form class="modal-body" id="createForm">
      <div class="grid">
        <input name="cedula" id="create_cedula" placeholder="Cédula" required>
        <input name="nombres" id="create_nombres" placeholder="Nombres" required>
        <input name="email" id="create_email" placeholder="Email">
        <input name="telefono" id="create_telefono" placeholder="Teléfono">
        <input name="direccion" id="create_direccion" placeholder="Dirección">
        <select name="estado" id="create_estado">
          <option value="1">Activo</option>
          <option value="0">Inactivo</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn secondary" onclick="closeCreateModal()">Cancelar</button>
        <button type="submit" class="btn primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDITAR -->
<div id="editBackdrop" class="modal-backdrop" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="editTitle">
    <div class="modal-header">
      <h3 id="editTitle">Editar Cliente</h3>
      <button class="icon-btn" type="button" onclick="closeEditModal()">✕</button>
    </div>

    <form class="modal-body" id="editForm">
      <input type="hidden" id="edit_id">

      <div class="grid">
        <input id="edit_cedula" placeholder="Cédula" required>
        <input id="edit_nombres" placeholder="Nombres" required>
        <input id="edit_email" placeholder="Email">
        <input id="edit_telefono" placeholder="Teléfono">
        <input id="edit_direccion" placeholder="Dirección">
        <select id="edit_estado">
          <option value="1">Activo</option>
          <option value="0">Inactivo</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn secondary" onclick="closeEditModal()">Cancelar</button>
        <button type="submit" class="btn primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
  // ===== CONFIG =====
  const API_BASE = <?= json_encode(API_BASE) ?>;

  const tbody = document.getElementById("tbodyClientes");
  const alertBox = document.getElementById("alert");

  const createBackdrop = document.getElementById("createBackdrop");
  const editBackdrop   = document.getElementById("editBackdrop");

  // ===== UI helpers =====
  function showAlert(msg, ok=true) {
    alertBox.style.display = "block";
    alertBox.textContent = msg;
    alertBox.style.background = ok ? "#e8f5e9" : "#ffebee";
    alertBox.style.color = ok ? "#1b5e20" : "#b71c1c";
    setTimeout(() => { alertBox.style.display = "none"; }, 2500);
  }

  function escapeHtml(s) {
    return (s ?? "").toString()
      .replaceAll("&","&amp;")
      .replaceAll("<","&lt;")
      .replaceAll(">","&gt;")
      .replaceAll('"',"&quot;")
      .replaceAll("'","&#039;");
  }

  // ===== API =====
  async function apiGetAll() {
    const res = await fetch(API_BASE, { method: "GET" });
    if (!res.ok) throw new Error("Error GET: " + res.status);
    return await res.json();
  }

  async function apiCreate(payload) {
    const res = await fetch(API_BASE, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });
    if (!res.ok) {
      const t = await res.text().catch(()=>"");
      throw new Error("Error POST: " + res.status + " " + t);
    }
    return true;
  }

  async function apiUpdate(id, payload) {
    const res = await fetch(`${API_BASE}/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });
    if (!res.ok) {
      const t = await res.text().catch(()=>"");
      throw new Error("Error PUT: " + res.status + " " + t);
    }
    return true;
  }

  async function apiDelete(id) {
    const res = await fetch(`${API_BASE}/${id}`, { method: "DELETE" });
    if (!res.ok) {
      const t = await res.text().catch(()=>"");
      throw new Error("Error DELETE: " + res.status + " " + t);
    }
    return true;
  }

  // ===== Render =====
  function renderTable(items) {
    tbody.innerHTML = "";
    if (!items || items.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7">No hay clientes.</td></tr>`;
      return;
    }

    for (const c of items) {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${escapeHtml(c.id)}</td>
        <td>${escapeHtml(c.cedula)}</td>
        <td>${escapeHtml(c.nombres)}</td>
        <td>${escapeHtml(c.email || "")}</td>
        <td>${escapeHtml(c.telefono || "")}</td>
        <td>${c.estado ? "Activo" : "Inactivo"}</td>
        <td class="actions">
          <button type="button" class="btn secondary">Editar</button>
          <button type="button" class="btn danger">Eliminar</button>
        </td>
      `;

      // Editar
      tr.querySelector(".btn.secondary").addEventListener("click", () => openEditModal(c));

      // Eliminar
      tr.querySelector(".btn.danger").addEventListener("click", async () => {
        if (!confirm("¿Eliminar cliente?")) return;
        try {
          await apiDelete(c.id);
          showAlert("Cliente eliminado ✅");
          await loadClientes();
        } catch (e) {
          showAlert(e.message, false);
        }
      });

      tbody.appendChild(tr);
    }
  }

  async function loadClientes() {
    try {
      const items = await apiGetAll();
      renderTable(items);
    } catch (e) {
      showAlert("No se pudo cargar la lista. " + e.message, false);
    }
  }

  // ===== Modales =====
  function openCreateModal() {
    document.getElementById("createForm").reset();
    document.getElementById("create_estado").value = "1";
    createBackdrop.classList.add("show");
    createBackdrop.setAttribute("aria-hidden", "false");
    setTimeout(() => document.getElementById("create_cedula").focus(), 50);
  }
  function closeCreateModal() {
    createBackdrop.classList.remove("show");
    createBackdrop.setAttribute("aria-hidden", "true");
  }

  function openEditModal(c) {
    document.getElementById("edit_id").value = c.id;
    document.getElementById("edit_cedula").value = c.cedula || "";
    document.getElementById("edit_nombres").value = c.nombres || "";
    document.getElementById("edit_email").value = c.email || "";
    document.getElementById("edit_telefono").value = c.telefono || "";
    document.getElementById("edit_direccion").value = c.direccion || "";
    document.getElementById("edit_estado").value = c.estado ? "1" : "0";

    editBackdrop.classList.add("show");
    editBackdrop.setAttribute("aria-hidden", "false");
    setTimeout(() => document.getElementById("edit_cedula").focus(), 50);
  }
  function closeEditModal() {
    editBackdrop.classList.remove("show");
    editBackdrop.setAttribute("aria-hidden", "true");
  }

  // cerrar clic fuera
  createBackdrop.addEventListener("click", e => { if (e.target === createBackdrop) closeCreateModal(); });
  editBackdrop.addEventListener("click", e => { if (e.target === editBackdrop) closeEditModal(); });

  // cerrar ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      if (createBackdrop.classList.contains("show")) closeCreateModal();
      if (editBackdrop.classList.contains("show")) closeEditModal();
    }
  });

  // ===== Forms (AJAX) =====
  document.getElementById("createForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
      cedula: document.getElementById("create_cedula").value.trim(),
      nombres: document.getElementById("create_nombres").value.trim(),
      email: document.getElementById("create_email").value.trim() || null,
      telefono: document.getElementById("create_telefono").value.trim() || null,
      direccion: document.getElementById("create_direccion").value.trim() || null,
      estado: document.getElementById("create_estado").value === "1"
    };

    try {
      await apiCreate(payload);
      closeCreateModal();
      showAlert("Cliente creado ✅");
      await loadClientes();
    } catch (e2) {
      showAlert(e2.message, false);
    }
  });

  document.getElementById("editForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = document.getElementById("edit_id").value;

    const payload = {
      cedula: document.getElementById("edit_cedula").value.trim(),
      nombres: document.getElementById("edit_nombres").value.trim(),
      email: document.getElementById("edit_email").value.trim() || null,
      telefono: document.getElementById("edit_telefono").value.trim() || null,
      direccion: document.getElementById("edit_direccion").value.trim() || null,
      estado: document.getElementById("edit_estado").value === "1"
    };

    try {
      await apiUpdate(id, payload);
      closeEditModal();
      showAlert("Cliente actualizado ✅");
      await loadClientes();
    } catch (e2) {
      showAlert(e2.message, false);
    }
  });

  // ===== init =====
  loadClientes();
</script>

</body>
</html>
