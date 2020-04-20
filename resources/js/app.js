import {setupAddItemModal} from "./components/modals/add";
import {getAndRenderItems, setupSortable} from "./components/itemList";

require('./bootstrap');

setupAddItemModal();

setupSortable();

getAndRenderItems();
