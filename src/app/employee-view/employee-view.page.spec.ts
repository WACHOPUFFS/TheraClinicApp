import { ComponentFixture, TestBed } from '@angular/core/testing';
import { EmployeeViewPage } from './employee-view.page';

describe('EmployeeViewPage', () => {
  let component: EmployeeViewPage;
  let fixture: ComponentFixture<EmployeeViewPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(EmployeeViewPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
