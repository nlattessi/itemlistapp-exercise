import Swal from "sweetalert2";
import axios from "axios";
import { getAndRenderItems } from "../itemList";

export default async e => {
    const item = e.target.closest("[data-item]").item;

    const { value: result } = await Swal.fire({
        title: "Are you sure you want to delete this item?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    });

    if (result) {
        try {
            await axios.delete(`/api/items/${item.id}`);
            await getAndRenderItems();
            Swal.fire("Deleted!", "Your item has been deleted.", "success");
        } catch (e) {
            console.log(e);
            Swal.fire({
                icon: "error",
                title: "Unable to delete item... :("
            });
        }
    }
};
