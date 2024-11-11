import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { NavController, ToastController, AlertController } from '@ionic/angular';
import { AuthService } from '../auth.service';

interface Empleado {
  [key: string]: any;
  departamento: string;
  puesto: string;
  turno: string;
  nombre: string;
  apellidoPaterno: string;
  apellidoMaterno: string;
  fechaNacimiento: string;
  lugarNacimiento: string;
  estadoCivil: string;
  sexo: string;
  curp: string;
  numeroSeguroSocial: string;
  rfc: string;
  correoElectronico: string;
  telefono: string;
  contactoEmergencia: string;
  numEmergencia: string;
  fechaInicio: string;
}


@Component({
  selector: 'app-add-employee',
  templateUrl: './add-employee.page.html',
  styleUrls: ['./add-employee.page.scss'],
})
export class AddEmployeePage implements OnInit {
  isSubmitting = false; // Variable para controlar el envío

  empleado: Empleado = {
    departamento: '',
    puesto: '',
    turno: '',
    nombre: '',
    apellidoPaterno: '',
    apellidoMaterno: '',
    fechaNacimiento: '',
    lugarNacimiento: '',
    estadoCivil: '',
    sexo: '',
    curp: '',
    numeroSeguroSocial: '',
    rfc: '',
    correoElectronico: '',
    telefono: '',
    contactoEmergencia: '',
    numEmergencia: '',
    fechaInicio: ''
   
  };

  departamentos: any[] = [];
  puestos: any[] = [];
  turnos: any[] = [];
  genders: any[] = [];
  maritalStatuses: any[] = [];
  curpValidationMessage: string = '';
  mostrarInfonavit: boolean = false;
  files: { [key: string]: File } = {};
  allFieldsCompleted: boolean = false;

  solicitudes: any[] = [];

  constructor(
    private http: HttpClient,
    private toastController: ToastController,
    private navCtrl: NavController,
    private authService: AuthService,
    private alertController: AlertController
  ) { }
  ngOnInit() {
    this.fetchSolicitudesUltimos15Dias();
    this.fetchGenders();
    this.fetchMaritalStatuses();
  }
  fetchSolicitudesUltimos15Dias() {
    const companyId = this.authService.selectedId;  // ID de la empresa
    const userId = this.authService.userId;  // ID del usuario que inició sesión (asegúrate de que esto esté disponible)
    const today = new Date();
    const fifteenDaysAgo = new Date();
    fifteenDaysAgo.setDate(today.getDate() - 15);

    const params = {
      company_id: companyId,
      user_id: userId,  // Se añade el parámetro del usuario
      fechaInicio: fifteenDaysAgo.toISOString().split('T')[0],
      fechaFin: today.toISOString().split('T')[0],
    };

    this.http.get<any>('https://siinad.mx/php/get_employee_requests.php', { params }).subscribe(
      data => {
        console.log('Solicitudes registradas:', data); // Verifica los datos recibidos

        // Verificar si 'data.solicitudes' es un array antes de asignarlo
        if (data && Array.isArray(data.solicitudes)) {
          this.solicitudes = data.solicitudes;
        } else {
          console.error('El dato recibido no es un array de solicitudes');
          this.solicitudes = []; // Asigna un array vacío si no es un array
        }
      },
      error => {
        console.error('Error al cargar solicitudes registradas', error);
        this.solicitudes = [];
      }
    );


  }
  getStatusDescription(status: string): string {
    switch (status.toLowerCase()) {  // Usar toLowerCase() para asegurarnos de que no haya errores por mayúsculas/minúsculas
      case 'incomplete':
        return 'Solicitud incompleta - Pendiente de información adicional';
      case 'pending':
        return 'Solicitud pendiente - En espera de aprobación por el administrador';
      case 'complete':
        return 'Solicitud completa - En espera de procesamiento por el administrativo';
      case 'finish':
        return 'Solicitud finalizada - Paciente dado de alta';
      default:
        return 'Estado desconocido';
    }
  }
  fetchGenders() {
    this.http.get<any[]>('https://siinad.mx/php/get_genders.php').subscribe(
      data => this.genders = data,
      error => console.error('Error al cargar géneros', error)
    );
  }
  fetchMaritalStatuses() {
    this.http.get<any[]>('https://siinad.mx/php/get_marital_statuses.php').subscribe(
      data => this.maritalStatuses = data,
      error => console.error('Error al cargar estados civiles', error)
    );
  }
  async onSubmit(form: NgForm) {
    if (this.isSubmitting) return; // Evita doble envío
    this.isSubmitting = true; // Bloquea envíos adicionales

    if (form.valid) {
      const status = this.allFieldsCompleted ? 'Pending' : 'Incomplete';
      const data = {
        ...this.empleado,
        companyId: this.authService.selectedId,
        userId: this.authService.userId,
        status
      };

      this.http.post('https://siinad.mx/php/submit_employee.php', data).subscribe(
        async (response: any) => {
          const employeeId = response.employee_id;
          const requestId = response.request_id;
          if (employeeId) {
            const alert = await this.alertController.create({
              header: status === 'Pending' ? 'Solicitud Enviada' : 'Información Guardada',
              message: status === 'Pending'
                ? `Paciente registrado exitosamente. Folio de solicitud: ${requestId}.`
                : `Información guardada. Tienes 3 días para completar la solicitud. Folio: ${requestId}`,
              buttons: ['OK']
            });
            await alert.present();
            this.goBack();
          } else {
            const toast = await this.toastController.create({
              message: 'Error al registrar paciente.',
              duration: 2000,
              color: 'danger'
            });
            toast.present();
          }
          this.isSubmitting = false; // Libera el bloqueo
        },
        async error => {
          const toast = await this.toastController.create({
            message: 'Error al registrar paciente.',
            duration: 2000,
            color: 'danger'
          });
          toast.present();
          this.isSubmitting = false; // Libera el bloqueo
        }
      );
    } else {
      const toast = await this.toastController.create({
        message: 'Por favor, complete todos los campos obligatorios.',
        duration: 2000,
        color: 'danger'
      });
      toast.present();
      this.validateAllFormFields(form);
      this.isSubmitting = false; // Libera el bloqueo
    }
  }

  checkAllFieldsCompleted() {
    this.allFieldsCompleted = !!(
      this.empleado.nombre &&
      this.empleado.apellidoPaterno &&
      this.empleado.apellidoMaterno 
    );
  }
  validateAllFormFields(form: NgForm) {
    Object.keys(form.controls).forEach(field => {
      const control = form.controls[field];
      control?.markAsTouched({ onlySelf: true });
    });
  }
  goBack() {
    this.navCtrl.back();
  }
}
