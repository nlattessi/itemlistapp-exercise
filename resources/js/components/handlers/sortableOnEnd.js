import Swal from "sweetalert2";
import axios from "axios";

export default async event => {
    const itemId = event.item.item.id;

    try {
        await axios.patch(`/api/items/${itemId}/position`, {
            position: event.newIndex
        });

        Swal.fire({
            position: "top-end",
            title: "Saved!",
            width: 150,
            showConfirmButton: false,
            timer: 1200,
            backdrop: false,
            allowOutsideClick: false,
            showClass: {
                popup: "animated slideInDown faster"
            },
            hideClass: {
                popup: "animated slideOutUp faster"
            }
        });
    } catch (e) {
        console.log(e);
        Swal.fire({
            icon: "error",
            title: "Unable to save order... :("
        });
    }
};
