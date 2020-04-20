import Swal from "sweetalert2";
import axios from "axios";
import { listen } from "../../utils";
import { getAndRenderItems } from "../itemList";

export const setupAddItemModal = () => {
    listen("click", "[data-add-modal-trigger]", openAddItemModal);
};

async function openAddItemModal() {
    const { value: dataFromUser } = await Swal.mixin({
        confirmButtonText: "Next &rarr;",
        showCancelButton: true,
        progressSteps: ["1", "2"]
    }).queue([
        {
            title: "New item",
            text: "Add a description",
            input: "textarea",
            inputValidator: description => {
                if (!description) {
                    return "You need to write something!";
                }

                if (description.length > 300) {
                    return "Max 300 characters :)";
                }
            }
        },
        {
            title: "New item",
            text: "Select an image",
            input: "file",
            inputAttributes: {
                accept: ".jpg,.gif,.png"
            },
            inputValidator: file => {
                if (file === null) {
                    return "You need to add an image!";
                }

                const validTypes = ["image/jpeg", "image/gif", "image/png"];
                if (!validTypes.includes(file.type)) {
                    return "File must be jpg, png or gif image :)";
                }

                return new Promise(resolve => {
                    const image = new Image();
                    image.onload = () => {
                        if (image.width !== 320 || image.height !== 320) {
                            resolve("Image must have 320px x 320px size :)");
                            return;
                        }
                        resolve();
                    };

                    image.src = URL.createObjectURL(file);
                });
            }
        }
    ]);

    if (dataFromUser === undefined) {
        return;
    }

    const [description, image] = dataFromUser;

    const formData = new FormData();
    formData.append("description", description);
    formData.append("image", image);

    try {
        await axios.post("/api/items", formData, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        });
        await getAndRenderItems();
        Swal.fire({
            title: "All done!",
            confirmButtonText: "Great!"
        });
    } catch (e) {
        console.log(e);
        Swal.fire({
            icon: "error",
            title: "Unable to create item... :("
        });
    }
}
