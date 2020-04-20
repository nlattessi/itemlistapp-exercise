import Swal from "sweetalert2";

export default async e => {
    const item = e.target.closest("[data-item]").item;

    Swal.fire("Item description", item.description);
};
