import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class DataStorageService {

  labelTitleApp: string = 'Sistema REPSE';

  //pantalla de inicio de sesión
  labeliniciarSesionTitle: string = 'Iniciar Sesión';
  labelIngreseDatosParaContinuar: string = 'Ingrese sus datos para continuar';
  labelContrasena: string = 'Contraseña';
  labelnombreUsuario: string = 'Usuario';
  buttonIniciarSesion: string = 'Iniciar Sesión';
  buttonRecuperarContrasena: string = 'Recuperar Contraseña';


  //pantalla de home y menu
  labelAdminEmpresas: string = 'Registrar cuenta de administrador para empresas';
  labelRegistrarCuentaUsuarioEmpresa: string = 'Registrar cuenta de usuario para la empresa'
  labelRegistrarSocioComercialRol: string = 'Registrar socio comercial (Rol)';
  labelSolicitarContactoEmpresa: string = 'Solicitar contacto a empresa';
  labelMensajesPendientes: string = 'Mensajes pendientes';
  labelAutorizarSocioComercial: string = 'Autorizar socio comercial';
  labelCerrarSesion: string = 'Cerrar Sesión';
  labelNivelUsuarioAdministradorGeneral: string = 'Administrador General de la Página';
  labelNivelUsuarioAdministradorEmpresaGeneral: string = 'Administrador único de la Empresa';
  labelNivelUsuarioAdministradorEmpresa: string = 'Administrador de la Empresa';
  labelNivelUsuarioAdministrativoEmpresa: string = 'Administrativo de la Empresa';
  labelNivelUsuarioSupervisorEmpresa: string = 'Supervisor de la Empresa';
  labelEmpresasPrincipalUsuario: string = 'Empresas principal del usuario';
  labelEmpresasLigadasUsuario: string = 'Empresas ligadas del usuario';
  labelSeleccioneEmpresaPrincipal: string = 'Seleccione una empresa principal:';

  buttonAgregarSocioComercial: string = 'Agregar socio comercial';
  buttonMiCodigoSocioComercial: string = 'Mi código de socio comercial';




  //pantalla de formularios de registro
  labelTipoRFC: string = 'Tipo de RFC';
  labelPersonaFisica: string = 'Persona Física';
  labelPersonaMoral: string = 'Persona Moral';
  labelRFC: string = 'RFC';
  labelComoApareceConstanciaFiscal: string = 'Como aparece en la constancia fiscal';
  labelAliasUsuario: string = 'Alias de usuario';
  labelNombreCompletoUsuario: string = 'Nombre completo del usuario';
  labelCorreo: string = 'Correo';
  labelValidacionCorreo: string = 'El correo electrónico debe contener el símbolo "@" y un dominio válido después del punto.';
  labelConfirmarContrasena: string = 'Confirmar contraseña';
  labelFechaInicioPeriodo: string = 'Fecha de inicio del periodo';
  labelFechaFinPeriodo: string = 'Fecha de fin del periodo';
  ButtonRegistrar: string = 'Registrar';

  //pantalla de registros (admin compañia)
  labelTipoUsuario: string = 'Tipo de usuario';
  labelTipoRol: string = 'Tipo de socio comercial';
  labelAdministrador: string = 'Administrador';
  labelSupervisor: string = 'Supervisor';
  labelAdministrativo: string = 'Administrativo';


  //vista registros (rol)
  labelCliente: string =  'Cliente';
  labelProveedor: string =  'Proveedor';
  labelClienteProveedor: string =  'Cliente - Proveedor';


  //modal de registro
  labelRegistarOtroUsuario: string = '¿Desea registrar otro usuario?';
  labelSi: string  = 'Si';
  labelNo: string = 'No';
  labelNoHome: string = 'No, volver a la pantalla principal';
  

  //pagina de confirmar socios comerciales
  labelSeleccioneSocioComercial: string = 'Seleccione un socio comercial:';
  labelDetallesSocioComercial: string = 'Detalles del socio comercial';
  buttonAceptarSocio: string = 'Aceptar socio comercial';
  buttonRechazarSocio: string = 'Rechazar socio comercial'
  labelRol: string = 'Rol'
  labelRolComo: string = 'Rol como socio comercial'
  labelNombreEmpresa: string = 'Nombre de la Empresa';


  //modal de confirmar socios
  labelContinuarConfirmarSocio: string = '¿Desea confirmar otro socio comercial?';

  //pagina de codigo de usuario
  labelCodigoDeUsuario: string = "Mi código de socio comercial";
  

  constructor() { }
}
