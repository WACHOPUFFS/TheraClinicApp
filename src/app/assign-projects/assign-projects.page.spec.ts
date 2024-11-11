import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AssignProjectsPage } from './assign-projects.page';

describe('AssignProjectsPage', () => {
  let component: AssignProjectsPage;
  let fixture: ComponentFixture<AssignProjectsPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(AssignProjectsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
