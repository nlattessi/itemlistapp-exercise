import { html, render } from "lit-html";
import { $ } from "../utils";
import Sortable from "sortablejs";
import Swal from "sweetalert2";
import axios from "axios";
import { itemTemplate } from "./item";
import sortableOnEnd from "./handlers/sortableOnEnd";

export const setupAddItemModal = () => {
    listen("click", "[data-add-modal-trigger]", openAddItemModal);
};

export const setupSortable = async () => {
    const simpleList = $("#simpleList");
    Sortable.create(simpleList, {
        animation: 150,
        filter: "button",
        ghostClass: "ghost",
        onEnd: sortableOnEnd
    });
};

const itemListTemplate = items =>
    html`
        ${items.map(
            item =>
                html`
                    ${itemTemplate(item)}
                `
        )}
    `;

export const getAndRenderItems = async () => {
    try {
        const { data } = await axios.get("/api/items");
        const items = data.data;

        const totalCountIttemsElement = $("[data-total-count-items]");
        totalCountIttemsElement.textContent = `Showing ${items.length} items`;

        const simpleList = $("#simpleList");
        render(itemListTemplate(items), simpleList);
    } catch (e) {
        console.log(e);
        Swal.fire({
            icon: "error",
            title: "Unable to get items... :("
        });
    }
};
