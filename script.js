const filter = document.querySelector("[data-filters]");
const addTask = document.querySelector("[data-add-task]");
const filterBtn = document.querySelector("[data-filter-btn]");
const addTaskBtn = document.querySelector("[data-add-task-btn]");

const toggle = (element) => {
  if (!filter.classList.contains("d-none") && element !== filter) {
    filter.classList.add("d-none");
  }

  if (!addTask.classList.contains("d-none") && element !== addTask) {
    addTask.classList.add("d-none");
  }

  element.classList.toggle("d-none");
};

const deleteTask = () => {
  const task = document.querySelector("[data-task]");
  const taskId = task.dataset.id;

  fetch(`index.php?record-id=${taskId}`, {
    method: "DELETE",
  });

  task.remove();
};

const openContextMenu = (e) => {
  e.preventDefault();

  if (!e.target.classList.contains("task")) return;

  const contextMenu = document.querySelector("[data-context-menu]");

  contextMenu.style.top = `${e.clientY + scrollY}px`;
  contextMenu.style.left = `${e.clientX + scrollX}px`;

  contextMenu.classList.toggle("d-none");

  document.addEventListener("click", () => {
    contextMenu.classList.add("d-none");
  });
};

filterBtn.addEventListener("click", () => {
  toggle(filter);
});

addTaskBtn.addEventListener("click", () => {
  toggle(addTask);
});

document.addEventListener("contextmenu", (e) => openContextMenu(e));

document
  .querySelector("[data-delete-task]")
  .addEventListener("click", deleteTask);

document.querySelectorAll("[data-task-check]").forEach((taskCheck) => {
  taskCheck.addEventListener("change", () => {
    const task = taskCheck.closest("[data-task]");
    const taskId = task.dataset.id;

    fetch(`index.php?record-id=${taskId}`, {
      method: "PUT",
    });

    task.classList.toggle("task--completed");
  });
});
