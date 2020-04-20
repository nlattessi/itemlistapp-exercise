import Swal from "sweetalert2";
import axios from "axios";
import { getAndRenderItems } from "../itemList";

export default async e => {
    const item = e.target.closest("[data-item]").item;

    const { value: dataFromUser } = await Swal.mixin({
        confirmButtonText: "Next &rarr;",
        showCancelButton: true,
        progressSteps: ["1", "2"]
    }).queue([
        {
            title: "Edit item",
            text: "(Optional) Change description",
            input: "textarea",
            inputValue: item.description,
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
            title: "Edit item",
            text: "(Optional) Change image",
            input: "file",
            inputAttributes: {
                accept: ".jpg,.gif,.png"
            },
            inputValidator: file => {
                if (file === null) {
                    return;
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

    if (description && description !== item.description) {
        formData.append("description", description);
    }

    if (image) {
        formData.append("image", image);
    }

    // Workaround for send files to a PATCH method in Laravel
    formData.append("_method", "PATCH");

    try {
        await axios.post(`/api/items/${item.id}`, formData, {
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
            title: "Unable to edit item... :("
        });
    }
};
