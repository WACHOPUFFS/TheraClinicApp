import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';
import { ModalController } from '@ionic/angular';
import { EmployeeDetailsComponent } from '../employee-details/employee-details.component';

interface Empleado {
  employee_id: number;
  first_name: string;
  middle_name?: string;
  last_name: string;
  employee_code: string;
  curp: string;
  social_security_number: string;
  rfc: string;
  email: string;
  phone_number: string;
  start_date: string;
}

@Component({
  selector: 'app-employee-view',
  templateUrl: './employee-view.page.html',
  styleUrls: ['./employee-view.page.scss'],
})
export class EmployeeViewPage implements OnInit {
  empleados: Empleado[] = [];
  empleadosFiltrados: Empleado[] = [];
  searchQuery: string = '';

  constructor(private modalController: ModalController, private http: HttpClient, public authService: AuthService, private router: Router) { }

  ngOnInit() {
    this.loadEmployees();
  }

  // Cargar todos los empleados de la empresa
  loadEmployees() {
    const companyId = this.authService.selectedId;
    this.http.get<{ success: boolean, employees: Empleado[] }>(`https://siinad.mx/php/get-pacients.php?company_id=${companyId}`).subscribe(
      response => {
        if (response.success) {
          this.empleados = response.employees;
          this.empleadosFiltrados = this.empleados;
        } else {
          console.error('Error al cargar los empleados de la empresa');
        }
      },
      error => {
        console.error('Error al cargar los empleados de la empresa:', error);
      }
    );
  }

  // Función para filtrar empleados en la búsqueda
  buscarEmpleados() {
    const searchLower = this.searchQuery.toLowerCase();
    this.empleadosFiltrados = this.empleados.filter(empleado =>
      empleado.first_name.toLowerCase().includes(searchLower) ||
      (empleado.middle_name && empleado.middle_name.toLowerCase().includes(searchLower)) ||
      empleado.last_name.toLowerCase().includes(searchLower) ||
      empleado.employee_code.toLowerCase().includes(searchLower)
    );
  }

  // Método para ver detalles del empleado
  async viewEmployeeDetails(employeeId: number) {
    const modal = await this.modalController.create({
      component: EmployeeDetailsComponent,
      componentProps: { employeeId: employeeId }
    });
    return await modal.present();
  }
}
