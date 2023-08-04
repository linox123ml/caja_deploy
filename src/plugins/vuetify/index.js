

import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

import "@mdi/font/css/materialdesignicons.css";
import defaults from './defaults'
import theme from './theme'

export default createVuetify({
    defaults,
    theme,
    components,
    directives,
});