import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';

interface Appointment {
  id: number;
  employeeId: number;
  therapistId: number;
  therapyType: string;
  date: string;
  time: string;
  notes: string;
  employeeName: string; // Campo mapeado desde employee_name
  therapistName: string; // Campo mapeado desde therapist_name
  createdAt: string; // Campo mapeado desde created_at
  updatedAt: string; // Campo mapeado desde updated_at
}

interface Employee {
  employee_id: number;
  first_name: string;
  last_name: string;
}

interface Therapist {
  id: number;
  name: string;
}

@Component({
  selector: 'app-employee-appointment',
  templateUrl: './employee-appointment.page.html',
  styleUrls: ['./employee-appointment.page.scss'],
})
export class EmployeeAppointmentPage implements OnInit {
  employees: Employee[] = [];
  therapists: Therapist[] = [];
  appointments: Appointment[] = [];
  appointment: Appointment = {
    id: 0,
    employeeId: 0,
    therapistId: 0,
    therapyType: '',
    date: '',
    time: '',
    notes: '',
    employeeName: '',
    therapistName: '',
    createdAt: '',
    updatedAt: ''
  };
  
  isEditMode: boolean = false;

  constructor(private http: HttpClient, private router: Router, private authService: AuthService) { }

  ngOnInit() {
    this.loadEmployees();
    this.loadTherapists();
    this.loadAppointments();
  }

  loadEmployees() {
    const companyId = this.authService.selectedId;
    this.http.get<{ success: boolean; employees: Employee[] }>(`https://siinad.mx/php/get-pacients.php?company_id=${companyId}`)
      .subscribe(response => {
        if (response.success && Array.isArray(response.employees)) {
          this.employees = response.employees;
          console.log('Employees loaded successfully:', this.employees);
        } else {
          console.error('Error: La respuesta no contiene un arreglo de empleados.');
          this.employees = []; // Inicializar como arreglo vacío en caso de error
        }
      }, error => {
        console.error('Error al cargar empleados:', error);
      });
  }

  loadTherapists() {
    const companyId = this.authService.selectedId;
    this.http.get<{ success: boolean; therapists: Therapist[] }>(`https://siinad.mx/php/get_therapists.php?company_id=${companyId}`)
      .subscribe(response => {
        if (response.success && Array.isArray(response.therapists)) {
          this.therapists = response.therapists;
          console.log('Therapists loaded successfully:', this.therapists);
        } else {
          console.error('Error: La respuesta no contiene un arreglo de terapeutas.');
          this.therapists = []; // Inicializar como arreglo vacío en caso de error
        }
      }, error => {
        console.error('Error al cargar terapeutas:', error);
      });
  }

  loadAppointments() {
    const companyId = this.authService.selectedId;
  
    this.http.get<{ success: boolean; appointments: any[] }>(`https://siinad.mx/php/get_appointments.php?company_id=${companyId}`)
      .subscribe(response => {
        if (response.success && Array.isArray(response.appointments)) {
          // Procesa la lista de citas y mapea los campos a la interfaz `Appointment`
          this.appointments = response.appointments.map(appointment => ({
            id: Number(appointment.id),
            employeeId: Number(appointment.employee_id),
            therapistId: Number(appointment.therapist_id),
            therapyType: appointment.therapy_type,
            date: appointment.date,
            time: appointment.time,
            notes: appointment.notes,
            employeeName: appointment.employee_name, // Mapeo directo de employee_name a employeeName
            therapistName: appointment.therapist_name, // Mapeo directo de therapist_name a therapistName
            createdAt: appointment.created_at,
            updatedAt: appointment.updated_at
          }));
          console.log('Appointments loaded successfully:', this.appointments);
        } else {
          console.error('Error: La respuesta no contiene un arreglo de citas.');
        }
      }, error => {
        console.error('Error al cargar citas:', error);
      });
  }
  
  
  
  

  createOrUpdateAppointment() {
    if (this.isEditMode) {
      this.updateAppointment();
    } else {
      this.createAppointment();
    }
  }

  createAppointment() {
    const appointmentData = {
      employee_id: this.appointment.employeeId,
      therapist_id: this.appointment.therapistId,
      therapy_type: this.appointment.therapyType,
      date: this.appointment.date,
      time: this.appointment.time,
      notes: this.appointment.notes
    };

    this.http.post('https://siinad.mx/php/create_employee_therapy_appointment.php', appointmentData)
      .subscribe(response => {
        console.log('Cita creada:', response);
        this.loadAppointments(); // Recargar la lista de citas después de crear
        this.resetForm(); // Reiniciar el formulario
      });
  }


  updateAppointment() {
    const appointmentData = {
      employee_id: this.appointment.employeeId,
      therapist_id: this.appointment.therapistId,
      therapy_type: this.appointment.therapyType,
      date: this.appointment.date,
      time: this.appointment.time,
      notes: this.appointment.notes
    };

    this.http.put(`https://siinad.mx/php/update_appointment.php?id=${this.appointment.id}`, appointmentData)
      .subscribe(response => {
        console.log('Cita actualizada:', response);
        this.loadAppointments();
        this.resetForm();
      });
  }

 

  editAppointment(appt: Appointment) {
    this.appointment = {
      id: appt.id,
      employeeId: appt.employeeId,
      therapistId: appt.therapistId,
      therapyType: appt.therapyType,
      date: appt.date,
      time: appt.time,
      notes: appt.notes,
      employeeName: appt.employeeName,
      therapistName: appt.therapistName,
      createdAt: appt.createdAt,
      updatedAt: appt.updatedAt
    };
    this.isEditMode = true;
  }
  
  

  resetForm() {
    this.appointment = {
      id: 0,
      employeeId: 0,
      therapistId: 0,
      therapyType: '',
      date: '',
      time: '',
      notes: '',
      employeeName: '',
      therapistName: '',
      createdAt: '',
      updatedAt: ''
    };
    this.isEditMode = false;
  }

  deleteAppointment(id: number) {
    // Muestra un mensaje de confirmación antes de eliminar
    if (confirm("¿Estás seguro de que deseas eliminar esta cita?")) {
      this.http.delete(`https://siinad.mx/php/delete_appointment.php?id=${id}`)
        .subscribe(
          response => {
            console.log('Cita eliminada:', response);
            this.loadAppointments(); // Recarga la lista de citas después de eliminar
          },
          error => {
            console.error('Error al eliminar la cita:', error);
          }
        );
    }
  }
  
  
}
