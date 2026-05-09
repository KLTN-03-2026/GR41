import * as yup from 'yup'

export const loginSchema = yup.object({
  email: yup.string().email('Email không hợp lệ').required('Bắt buộc'),
  password: yup.string().required('Bắt buộc'),
})

export const registerSchema = yup.object({
  name: yup.string().required('Bắt buộc').min(2, 'Tối thiểu 2 ký tự'),
  email: yup.string().email('Email không hợp lệ').required('Bắt buộc'),
  password: yup.string().min(8, 'Mật khẩu tối thiểu 8 ký tự').required('Bắt buộc'),
  password_confirmation: yup
    .string()
    .oneOf([yup.ref('password')], 'Mật khẩu xác nhận không khớp')
    .required('Bắt buộc'),
  phone: yup.string().nullable(),
  student_code: yup.string().nullable(),
})

export const changePasswordSchema = yup.object({
  current_password: yup.string().required('Bắt buộc'),
  password: yup.string().min(8, 'Mật khẩu tối thiểu 8 ký tự').required('Bắt buộc'),
  password_confirmation: yup
    .string()
    .oneOf([yup.ref('password')], 'Không khớp')
    .required('Bắt buộc'),
})
