import { Component, Input, OnInit } from '@angular/core';
import { AlertController, ModalController, LoadingController, ToastController } from '@ionic/angular';

import { HttpClient } from '@angular/common/http';
import { AuthService } from '../auth.service';  // Asegúrate de tener el servicio de autenticación
import * as JSZip from 'jszip';
import { saveAs } from 'file-saver';
import * as pdfjsLib from 'pdfjs-dist';

interface Empleado {
  employee_id: number;
  employee_code: string;
  first_name: string;
  middle_name?: string;
  last_name: string;
  full_name: string;
  photo?: string;
  birth_date: string;
  birth_place?: string;
  curp: string;
  curp_initials?: string;
  curp_final?: string;
  social_security_number: string;
  unique_medical_unit_code?: string;
  rfc: string;
  homoclave?: string;
  bank_account_number?: string;
  bank_branch?: string;
  bank_name?: string;
  employee_status: string;
  daily_salary?: number;
  daily_salary_date?: string;
  variable_salary?: number;
  variable_salary_date?: string;
  average_salary?: number;
  average_salary_date?: string;
  integrated_salary?: number;
  integrated_salary_date?: string;
  calculated_salary?: number;
  affected_salary?: number;
  extraordinary_calculated_salary?: number;
  extraordinary_affected_salary?: number;
  salary_modification_net?: number;
  start_date: string;
  contract_type?: string;
  employee_type?: string;
  payment_base?: string;
  payment_method?: string;
  salary_zone?: string;
  ptu_calculation?: string;
  christmas_bonus_calculation?: string;
  imss_registration?: string;
  imss_deregistration?: string;
  phone_number: string;
  postal_code?: string;
  address?: string;
  city?: string;
  state?: string;
  father_name?: string;
  mother_name?: string;
  afore_number?: string;
  termination_date?: string;
  termination_reason?: string;
  settlement_base_salary?: number;
  extra_field_1?: string;
  extra_field_2?: string;
  extra_numeric_field_1?: number;
  clabe?: string;
  email: string;
  emergency_contact_name?: string;
  emergency_contact_number?: string;
  created_at: string;
  updated_at: string;
  department_id: number; // ID del departamento
  position_id: number;   // ID del puesto
  shift_id: number;      // ID del turno
  gender_id: number;
  marital_status_id: number;
  department_name: string;
  position_name: string;
  shift_name: string;
  net_balance: number;

   // Campos adicionales
   request_id?: number;             // Folio de registro del empleado
   folio_number_imss?: string;      // Folio del IMSS
   lot_number_imss?: string;        // Lote del IMSS
}

interface EmployeeFile {
  file_id: number;
  file_type: string;
  file_name: string;
  file_path: string;
  uploaded_at: string;
}


interface ApiResponse {
  success: boolean;
  message?: string;
  error?: string;
}

@Component({
  selector: 'app-employee-details',
  templateUrl: './employee-details.component.html',
  styleUrls: ['./employee-details.component.scss'],
})
export class EmployeeDetailsComponent implements OnInit {
  @Input() employeeId: number; // Recibir el ID del empleado
  employee: Empleado = {
    employee_id: 0,
    employee_code: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    full_name: '',
    photo: '',
    birth_date: '',
    birth_place: '',
    curp: '',
    curp_initials: '',
    curp_final: '',
    social_security_number: '',
    unique_medical_unit_code: '',
    rfc: '',
    homoclave: '',
    bank_account_number: '',
    bank_branch: '',
    bank_name: '',
    employee_status: '',
    daily_salary: 0,
    daily_salary_date: '',
    variable_salary: 0,
    variable_salary_date: '',
    average_salary: 0,
    average_salary_date: '',
    integrated_salary: 0,
    integrated_salary_date: '',
    calculated_salary: 0,
    affected_salary: 0,
    extraordinary_calculated_salary: 0,
    extraordinary_affected_salary: 0,
    salary_modification_net: 0,
    start_date: '',
    contract_type: '',
    employee_type: '',
    payment_base: '',
    payment_method: '',
    salary_zone: '',
    ptu_calculation: '',
    christmas_bonus_calculation: '',
    imss_registration: '',
    imss_deregistration: '',
    phone_number: '',
    postal_code: '',
    address: '',
    city: '',
    state: '',
    father_name: '',
    mother_name: '',
    afore_number: '',
    termination_date: '',
    termination_reason: '',
    settlement_base_salary: 0,
    extra_field_1: '',
    extra_field_2: '',
    extra_numeric_field_1: 0,
    clabe: '',
    email: '',
    emergency_contact_name: '',
    emergency_contact_number: '',
    created_at: '',
    updated_at: '',
    department_id: 0,
    position_id: 0,
    shift_id: 0,
    gender_id: 0,
    marital_status_id: 0,
    department_name: '',
    position_name: '',
    shift_name: '',
    net_balance: 0
  };

  files: EmployeeFile[] = []; // Almacena los archivos del empleado
  departamentos: any[] = [];
  puestos: any[] = [];
  turnos: any[] = [];

  genders: any[] = [];  // Aquí se almacenarán los géneros
  maritalStatuses: any[] = [];  // Lista de estados civiles

  expectedFileTypes: string[] = [
    'ineFrente',
    'ineReverso',
    'constanciaFiscal',
    'numSeguroSocialArchivo',
    'actaNacimiento',
    'CURPFile',
    'comprobanteDomicilio',
    'cuentaInterbancaria',
    'retencionInfonavit',
    'antecedentesPenales',
    'comprobanteEstudios',
    'archivoIMSS'
  ];


  employeeFiles: EmployeeFile[] = []; // Array de archivos

  selectedFiles: { [key: number]: File } = {}; // Objeto para almacenar los archivos seleccionados por ID de archivo

  constructor(private loadingController: LoadingController, private toastController: ToastController, private alertController: AlertController, private modalController: ModalController, private http: HttpClient, private authService: AuthService) { }

  ngOnInit() {
    // Obtener detalles del empleado y luego iniciar la carga de departamentos, puestos y turnos
    this.getGenders();  // Obtener la lista de géneros
    this.getMaritalStatuses();  // Obtener la lista de estados civiles
    this.getDepartamentos();
    this.getPuestos();
    this.getTurnos();
    this.getEmployeeDetails(this.employeeId);

    // Verificar los archivos cargados
    console.log("Archivos del empleado:", this.employeeFiles);

  }

  // Obtener los detalles del empleado desde el servidor
  getEmployeeDetails(employeeId: number) {
    this.http.get<{ success: boolean, employee: Empleado, files: EmployeeFile[] }>(`https://siinad.mx/php/get_employee_details.php?employee_id=${employeeId}`)
      .subscribe(response => {
        if (response.success) {
          this.employee = response.employee;
          this.employeeFiles = response.files as EmployeeFile[]; // Asignar los archivos obtenidos
          console.log('Archivos del empleado:', this.employeeFiles); // Depura la respuesta para ver si hay archivos
          // Obtener departamentos



        } else {
          console.error('Error al cargar los detalles del empleado');
        }
      });
  }


  // Cerrar el modal
  closeModal() {
    this.modalController.dismiss();
  }

  downloadFile(filePath: string) {
    const fullUrl = `https://www.siinad.mx/php/${filePath}`; // URL completa del archivo
    window.open(fullUrl, '_blank'); // Abrir el archivo en una nueva pestaña o iniciar la descarga
  }

  deleteFile(fileId: number) {
    if (confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
      // Petición HTTP al backend para eliminar el archivo
      this.http.post<ApiResponse>('https://www.siinad.mx/php/delete_employee_file.php', { file_id: fileId })
        .subscribe(
          (response: ApiResponse) => {
            if (response.success) {
              console.log('Archivo eliminado con éxito');
              // Remover el archivo de la lista de archivos locales para que ya no se muestre en la UI
              this.employeeFiles = this.employeeFiles.filter(file => file.file_id !== fileId);
            } else {
              console.error('Error al eliminar el archivo:', response.message);
              alert('Ocurrió un error al intentar eliminar el archivo. Inténtalo de nuevo.');
            }
          },
          error => {
            // Manejo de errores de la petición
            console.error('Error de red o servidor:', error);
            alert('Error al conectar con el servidor. Verifica tu conexión.');
          }
        );
    }
  }


// Método para obtener etiquetas amigables para el tipo de archivo
getLabelForFileType(fileType: string): string {
  switch (fileType) {
    case 'ineFrente':
      return 'Identificación INE (Frente)';
    case 'ineReverso':
      return 'Identificación INE (Reverso)';
    case 'constanciaFiscal':
      return 'Constancia de Situación Fiscal';
    case 'numSeguroSocialArchivo':
      return 'Número de Seguro Social y Unidad Médica';
    case 'actaNacimiento':
      return 'Acta de Nacimiento';
    case 'CURPFile':
      return 'CURP';
    case 'comprobanteDomicilio':
      return 'Comprobante de Domicilio';
    case 'cuentaInterbancaria':
      return 'Cuenta Interbancaria';
    case 'retencionInfonavit':
      return 'Carta de Retención de Infonavit';
    case 'antecedentesPenales':
      return 'Carta de No Antecedentes Penales';
    case 'comprobanteEstudios':
      return 'Comprobante de Estudios';
    case 'archivoIMSS':
      return 'Archivo del alta en el IMSS';
    default:
      return 'Archivo';
  }
}






async extractFolioYLote(file: File) {
  // Configurar la ruta del worker para pdf.js
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

  const arrayBuffer = await file.arrayBuffer();
  const uint8Array = new Uint8Array(arrayBuffer); // Convertir ArrayBuffer a Uint8Array
  const pdfDocument = await pdfjsLib.getDocument({ data: uint8Array }).promise;

  let folio = '';
  let lote = '';

  // Procesar solo la primera página
  const page = await pdfDocument.getPage(1);
  const textContent = await page.getTextContent();

  const pageText = textContent.items.map((item: any) => item.str).join(' ');
  console.log(`Texto de la primera página:`, pageText); // Verificar el texto extraído

  // Capturar todas las secuencias de números de cualquier longitud
  const numberMatches = pageText.match(/\d+/g);

  if (numberMatches) {
    console.log('Secuencias numéricas encontradas:', numberMatches);

    // Asignar las primeras secuencias de números al lote y folio si están presentes
    if (numberMatches.length > 0) {
      folio = numberMatches[0]; // Primera secuencia numérica
    }
    if (numberMatches.length > 1) {
      lote = numberMatches[1]; // Segunda secuencia numérica
    }

    console.log('Folio asignado:', folio);
    console.log('Lote asignado:', lote);
  }

  return { folio, lote };
}







async onFileChange(event: any, fileType: string, fileId?: number) {
  const file = event.target.files[0];
  if (!file) {
    console.error('No se seleccionó ningún archivo.');
    return;
  }

  const formData = new FormData();
  formData.append('employee_id', this.employee.employee_id.toString());
  formData.append(fileType, file);

  if (fileId) {
    formData.append('file_id', fileId.toString());
  }

  // Extraer folio y lote solo si el archivo es "archivoIMSS"
  let folio = '';
  let lote = '';
  if (fileType === 'archivoIMSS' && file.type === 'application/pdf') {
    const result = await this.extractFolioYLote(file);
    folio = result.folio;
    lote = result.lote;

    // Verificar si folio y lote están presentes
    if (!folio || !lote) {
      await this.showErrorAlert();
      return; // Detener el proceso de subida
    }

    formData.append('folio', folio);
    formData.append('lote', lote);
  }

  // Enviar el archivo al servidor
  this.http.post('https://www.siinad.mx/php/update_upload_files.php', formData)
    .subscribe(async response => {
      console.log('Respuesta del servidor:', response);
      this.getEmployeeDetails(this.employee.employee_id); // Refresca los detalles del empleado

      // Mostrar alerta de confirmación
      if (fileType === 'archivoIMSS') {
        await this.showAlert(folio, lote);
      }
    }, error => {
      console.error('Error al subir el archivo:', error);
    });
}

// Mostrar alerta de éxito
async showAlert(folio: string, lote: string) {
  const alert = await this.alertController.create({
    header: 'Archivo Subido',
    message: `Se ha subido la alta del IMSS con el folio: ${folio} y lote: ${lote}.`,
    buttons: ['OK']
  });

  await alert.present();
}

// Mostrar alerta de error
async showErrorAlert() {
  const alert = await this.alertController.create({
    header: 'Error',
    message: 'No se encontró el folio o lote en el archivo. No se subió el archivo.',
    buttons: ['OK']
  });

  await alert.present();
}

  triggerFileInput(inputId: string) {
    const fileInput = document.getElementById(inputId) as HTMLElement;
    console.log('Intentando seleccionar input con ID:', inputId); // Verificar el ID del input
    if (fileInput) {
      fileInput.click(); // Simula el clic en el input oculto
    } else {
      console.error('No se pudo encontrar el input de archivo con ID:', inputId);
    }
  }

  onPhotoChange(event: any) {
    const file = event.target.files[0]; // Captura el archivo

    if (file) {
      const reader = new FileReader();

      reader.onload = () => {
        // Si se selecciona un archivo, asigna la vista previa de la imagen
        if (typeof reader.result === 'string') {
          this.employee.photo = reader.result; // Previsualización de la foto
        }
      };

      reader.readAsDataURL(file); // Leer archivo como URL

      // Llama al método para subir el archivo al servidor
      this.uploadPhoto(file);
    }
  }

  async uploadPhoto(file: File) {
    const formData = new FormData();
    formData.append('employee_id', this.employee.employee_id.toString());
    formData.append('file', file);
    formData.append('file_type', 'photo');
  
    const loading = await this.loadingController.create({
      message: 'Cargando...',
    });
    await loading.present();
  
    this.http.post('https://siinad.mx/php/upload_employee_photo.php', formData).subscribe(
      async response => {
        await loading.dismiss();
        const successToast = await this.toastController.create({
          message: 'Foto subida exitosamente',
          duration: 2000,
          color: 'success'
        });
        await successToast.present();
        console.log('Foto subida exitosamente', response);
      },
      async error => {
        await loading.dismiss();
        const errorToast = await this.toastController.create({
          message: 'Error al subir la foto',
          duration: 2000,
          color: 'danger'
        });
        await errorToast.present();
        console.error('Error al subir la foto', error);
      }
    );
  }

  getFileByType(fileType: string): EmployeeFile | null {
    return this.employeeFiles.find(file => file.file_type === fileType) || null;
  }

  async saveGeneralInfo() {
    const generalInfo = {
      employee_id: this.employee.employee_id,
      employee_code: this.employee.employee_code,
      first_name: this.employee.first_name,
      middle_name: this.employee.middle_name,
      last_name: this.employee.last_name,
      birth_date: this.employee.birth_date,
      birth_place: this.employee.birth_place,
      curp: this.employee.curp,
      rfc: this.employee.rfc,
      phone_number: this.employee.phone_number,
      email: this.employee.email,
      gender_id: this.employee.gender_id,
      marital_status_id: this.employee.marital_status_id,
      company_id: this.authService.selectedId
    };
  
    const loading = await this.loadingController.create({
      message: 'Guardando información...',
    });
    await loading.present();
  
    this.http.post<any>('https://www.siinad.mx/php/update_general_info.php', generalInfo).subscribe(
      async response => {
        await loading.dismiss();
        if (response.success) {
          const successToast = await this.toastController.create({
            message: 'Información general actualizada con éxito',
            duration: 2000,
            color: 'success'
          });
          await successToast.present();
          console.log('Información general actualizada con éxito', response);
        } else {
          const errorToast = await this.toastController.create({
            message: response.message,
            duration: 2000,
            color: 'danger'
          });
          await errorToast.present();
          console.error('Error en la respuesta del servidor:', response.error || 'Sin detalles adicionales');
        }
      },
      async error => {
        await loading.dismiss();
        const errorToast = await this.toastController.create({
          message: 'Error al actualizar la información general',
          duration: 2000,
          color: 'danger'
        });
        await errorToast.present();
        console.error('Error al hacer la solicitud:', error);
        if (error.error && error.error.error) {
          console.error('Detalles del error del servidor:', error.error.error);
        }
      }
    );
  }

  async saveFinancialInfo() {
    const financialInfo = {
      employee_id: this.employee.employee_id,
      social_security_number: this.employee.social_security_number,
      bank_account_number: this.employee.bank_account_number,
      bank_name: this.employee.bank_name,
      bank_branch: this.employee.bank_branch,
      clabe: this.employee.clabe
    };
  
    const loading = await this.loadingController.create({
      message: 'Guardando información financiera...',
    });
    await loading.present();
  
    this.http.post<any>('https://www.siinad.mx/php/update_financial_info.php', financialInfo).subscribe(
      async response => {
        await loading.dismiss();
        const successToast = await this.toastController.create({
          message: 'Información financiera actualizada con éxito',
          duration: 2000,
          color: 'success'
        });
        await successToast.present();
        console.log('Información financiera actualizada con éxito', response);
      },
      async error => {
        await loading.dismiss();
        const errorToast = await this.toastController.create({
          message: 'Error al actualizar la información financiera',
          duration: 2000,
          color: 'danger'
        });
        await errorToast.present();
        console.error('Error al actualizar la información financiera:', error);
        if (error.error && error.error.error) {
          console.error('Detalles del error del servidor:', error.error.error);
        }
      }
    );
  }

  async saveWorkInfo() {
    const workInfo = {
      employee_id: this.employee.employee_id,
      department_id: this.employee.department_id,
      position_id: this.employee.position_id,
      shift_id: this.employee.shift_id,
      start_date: this.employee.start_date,
      employee_status: this.employee.employee_status,
      net_balance: this.employee.net_balance,
      daily_salary: this.employee.daily_salary
    };
  
    const loading = await this.loadingController.create({
      message: 'Guardando información laboral...',
    });
    await loading.present();
  
    this.http.post<any>('https://www.siinad.mx/php/update_work_info.php', workInfo).subscribe(
      async response => {
        await loading.dismiss();
        const successToast = await this.toastController.create({
          message: 'Información laboral actualizada con éxito',
          duration: 2000,
          color: 'success'
        });
        await successToast.present();
        console.log('Información laboral actualizada con éxito', response);
      },
      async error => {
        await loading.dismiss();
        const errorToast = await this.toastController.create({
          message: 'Error al actualizar la información laboral',
          duration: 2000,
          color: 'danger'
        });
        await errorToast.present();
        console.error('Error al actualizar la información laboral:', error);
        if (error.error && error.error.error) {
          console.error('Detalles del error del servidor:', error.error.error);
        }
      }
    );
  }


  async saveEmergencyContact() {
    const emergencyContactInfo = {
      employee_id: this.employee.employee_id,
      emergency_contact_name: this.employee.emergency_contact_name,
      emergency_contact_number: this.employee.emergency_contact_number
    };
  
    const loading = await this.loadingController.create({
      message: 'Guardando contacto de emergencia...',
    });
    await loading.present();
  
    this.http.post<any>('https://www.siinad.mx/php/update_emergency_contact.php', emergencyContactInfo).subscribe(
      async response => {
        await loading.dismiss();
        const successToast = await this.toastController.create({
          message: 'Contacto de emergencia actualizado con éxito',
          duration: 2000,
          color: 'success'
        });
        await successToast.present();
        console.log('Contacto de emergencia actualizado con éxito', response);
      },
      async error => {
        await loading.dismiss();
        const errorToast = await this.toastController.create({
          message: 'Error al actualizar el contacto de emergencia',
          duration: 2000,
          color: 'danger'
        });
        await errorToast.present();
        console.error('Error al actualizar el contacto de emergencia:', error);
        if (error.error && error.error.error) {
          console.error('Detalles del error del servidor:', error.error.error);
        }
      }
    );
  }

  getGenders() {
    this.http.get<any[]>('https://www.siinad.mx/php/get_genders.php')
      .subscribe(response => {
        this.genders = response;  // Asignar la lista de géneros al array
        console.log(this.genders);  // Verificar en la consola que se recibieron los datos
      }, error => {
        console.error('Error al obtener los géneros', error);  // Manejo de errores
      });
  }

  // Método para obtener los estados civiles desde el backend
  getMaritalStatuses() {
    this.http.get<any[]>('https://www.siinad.mx/php/get_marital_statuses.php')
      .subscribe(response => {
        this.maritalStatuses = response;  // Asignar la lista de estados civiles al array
        console.log(this.maritalStatuses);  // Verificar en la consola que se recibieron los datos
      }, error => {
        console.error('Error al obtener los estados civiles', error);  // Manejo de errores
      });
  }


  // Obtener la lista de departamentos
  getDepartamentos() {
    const companyId = this.authService.selectedId; // Obtener el ID de la empresa desde AuthService
    this.http.get<any[]>(`https://siinad.mx/php/get_departments.php?company_id=${companyId}`)
      .subscribe(response => {
        this.departamentos = response.map(departamento => ({
          ...departamento,
          department_id: departamento.department_id.toString() // Convertir a cadena
        }));
      }, error => {
        console.error('Error al cargar los departamentos:', error);
      });

  }

  getPuestos() {
    const companyId = this.authService.selectedId; // Obtener el ID de la empresa desde AuthService
    this.http.get<any[]>(`https://siinad.mx/php/get_positions.php?company_id=${companyId}`)
      .subscribe(response => {
        this.puestos = response.map(puesto => ({
          ...puesto,
          position_id: puesto.position_id.toString() // Convertir a cadena
        }));
      }, error => {
        console.error('Error en la solicitud HTTP de puestos:', error);
      });
  }

  getTurnos() {
    const companyId = this.authService.selectedId; // Obtener el ID de la empresa desde AuthService
    this.http.get<any[]>(`https://siinad.mx/php/get_shifts.php?company_id=${companyId}`)
      .subscribe(response => {
        this.turnos = response.map(turno => ({
          ...turno,
          shift_id: turno.shift_id.toString() // Convertir a cadena
        }));
      }, error => {
        console.error('Error en la solicitud HTTP de turnos:', error);
      });
  }

  async downloadEmployeeData() {
    const zip = new JSZip();
    const folderName = this.employee.full_name.replace(/\s+/g, '_');
    const folder = zip.folder(folderName);

    // Verificar si la carpeta principal se creó correctamente
    if (!folder) {
      console.error(`No se pudo crear la carpeta para el empleado ${this.employee.full_name}`);
      return;
    }

    // Información del empleado en formato TXT
    const employeeData = `
    Código de Empleado: ${this.employee.employee_code || ''}
    Folio: ${this.employee.request_id || ''}
    Folio IMSS: ${this.employee.folio_number_imss || ''}
    Número de Lote IMSS: ${this.employee.lot_number_imss || ''}
    Estado en el IMSS: ${this.employee.request_id && this.employee.folio_number_imss && this.employee.lot_number_imss ? 'Activo en el IMSS' : 'No activo en el IMSS'}
    
    Nombre: ${this.employee.first_name || ''}
    Apellido Paterno: ${this.employee.last_name || ''}
    Apellido Materno: ${this.employee.middle_name || ''}
    Fecha de Nacimiento: ${this.employee.birth_date || ''}
    Lugar de Nacimiento: ${this.employee.birth_place || ''}
    CURP: ${this.employee.curp || ''}
    RFC: ${this.employee.rfc || ''}
    Teléfono: ${this.employee.phone_number || ''}
    Correo Electrónico: ${this.employee.email || ''}
    Género: ${this.getGenderName(this.employee.gender_id) || ''}
    Estado Civil: ${this.getMaritalStatusName(this.employee.marital_status_id) || ''}
    
    Información Financiera y Bancaria:
    Número de Seguro Social: ${this.employee.social_security_number || ''}
    Número de Cuenta Bancaria: ${this.employee.bank_account_number || ''}
    Banco: ${this.employee.bank_name || ''}
    Sucursal del Banco: ${this.employee.bank_branch || ''}
    CLABE Interbancaria: ${this.employee.clabe || ''}
    
    Información del Trabajo:
    Departamento: ${this.employee.department_name || 'Sin asignar'}
    Puesto: ${this.employee.position_name || 'Sin asignar'}
    Turno: ${this.employee.shift_name || 'Sin asignar'}
    Fecha de Inicio: ${this.employee.start_date || ''}
    Estado del Empleado: ${this.employee.employee_status || ''}
    Sueldo Neto: ${this.employee.net_balance || ''}
    Sueldo Diario: ${this.employee.daily_salary || ''}
    
    Contacto de Emergencia:
    Nombre: ${this.employee.emergency_contact_name || ''}
    Número de Emergencia: ${this.employee.emergency_contact_number || ''}
  `;


    folder.file('informacion_usuario.txt', employeeData);

    // Crear la subcarpeta "archivos" dentro de la carpeta del empleado
    const archivosFolder = folder.folder('archivos');

    // Verificar si la subcarpeta se creó correctamente
    if (!archivosFolder) {
      console.error(`No se pudo crear la subcarpeta "archivos" para el empleado ${this.employee.full_name}`);
      return;
    }

    // Añadir archivos del empleado a la subcarpeta "archivos" con `fileType` como nombre de archivo
    for (const file of this.employeeFiles) {
      try {
        const fileBlob = await this.getFileBlob(file.file_path);
        const fileName = `${file.file_type}_${this.employee.employee_code}_${file.file_name}`; // Usa `file_type_employee_code` como prefijo para el nombre del archivo
        archivosFolder.file(fileName, fileBlob);
      } catch (error) {
        console.error(`Error al obtener el archivo ${file.file_name}:`, error);
      }
    }

    // Genera el archivo ZIP y descarga
    zip.generateAsync({ type: 'blob' }).then((blob) => {
      saveAs(blob, `${folderName}.zip`);
    });
  }

  getGenderName(genderId: number): string {
    const gender = this.genders.find(g => g.gender_id === genderId);
    return gender ? gender.gender_name : '';
  }

  getMaritalStatusName(statusId: number): string {
    const status = this.maritalStatuses.find(s => s.status_id === statusId);
    return status ? status.status_name : '';
  }


  async getFileBlob(filePath: string): Promise<Blob> {
    try {
      // Codifica la ruta del archivo
      const encodedFilePath = encodeURIComponent(filePath);

      // Llama a `download.php` pasando la ruta del archivo
      const response = await fetch(`https://siinad.mx/php/download.php?file_path=${encodedFilePath}`);

      if (!response.ok) {
        throw new Error(`Error al obtener el archivo: ${response.statusText}`);
      }

      return await response.blob();
    } catch (error) {
      console.error(`Error al obtener el archivo desde ${filePath}:`, error);
      throw error;
    }
  }
}