methods:
  post: [password, password2]
names:
  password:
    required:     true
    required_msg: debe digitar un nombre de usuario
    validators:   wordValidator
  password2:
    required:     true
    required_msg: debe digitar la clave anterior
    validators:   wordValidator
wordValidator:
    class:         sfStringValidator
    param:
      min:         8
      min_error:   la clave debe tener por lo menos 8 caracteres             
