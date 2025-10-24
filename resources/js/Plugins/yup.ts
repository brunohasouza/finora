import { setLocale } from 'yup';

setLocale({
    mixed: {
        required: 'Este campo é obrigatório.',
    },
    string: {
        email: 'Insira um e-mail válido.',
        min: 'Este campo deve ter no mínimo ${min} caracteres.',
    },
});
