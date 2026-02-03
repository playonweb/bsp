# System Design

## Assumption

The `core` framework files should not be changed, even when there is some scope for improvements, to avoid losing the originality of the framework.

## Functional Requirements

### User Management
- The system must display a list of existing users in a tabular format, including their **Name**, **E-mail**, **City**, and **Phone Number**.
- The system must provide a form to create new user records.
- New records must include a phone number column in the database and the UI.

### Search and Filtering
- The system must include client-side JavaScript functionality to filter table rows by **city**.

### Data Submission
- Form submission must be handled using **AJAX** to prevent page reloads.
- The system must validate new records before they are saved to the database.

### Data Persistence
- The system must store user data in a **MySQL** database.
- The system must record the **creation timestamp** (`created_at`) for every new record.

---

## Non-Functional Requirements

### Usability and User Experience (UX)
- The application must be styled using **Bootstrap** or **Tailwind CSS** to ensure a presentable interface.
- The user interface should be designed as a real-world product that people enjoy using, moving beyond a simple task list.
- The table must use visual cues for readability, such as **highlighting every other row**.
- The form must use standard layouts (like Bootstrap's `form-horizontal`) for better accessibility and aesthetics.

### Performance and Efficiency
- The project should be completed within a **4-hour time limit**, requiring the developer to prioritize high-impact features.
- The developer must demonstrate the **"Two Sides" philosophy**, balancing rapid development with polished, production-ready code where it matters most.

### Scalability and Maintenance
- The code must be **"production-ready"**, implying it should be clean, modular, and follow the provided 'framework' structure.
- The system must utilize a **Model-View-Controller (MVC)** style pattern as established by the `App` and `BaseModel` classes.

### Deployment and Distribution
- The application must be deployable to a **free hosting service** with a publicly accessible production link.
- Source code must be managed in a **private GitHub repository** with specified collaborators invited.

### Security (Implicit)
- Though the provided framework uses basic escaping, **"production-ready" standards** suggest implementing robust protection against **SQL injection** and ensuring data integrity.