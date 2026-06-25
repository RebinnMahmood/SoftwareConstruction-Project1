// Enhanced client-side navigation & form handling with user roles

window.addEventListener('DOMContentLoaded', () => {
  // Ensure default admin exists
  let users = JSON.parse(localStorage.getItem('users')) || [];
  if (!users.find(u => u.email === 'admin@gmail.com')) {
    users.push({ name: 'admin', email: 'admin@gmail.com', password: 'adminadmin', role: 'admin' });
    localStorage.setItem('users', JSON.stringify(users));
  }

  // Load user info from localStorage
  const currentUser = JSON.parse(localStorage.getItem('currentUser'));

  // Update header if logged in
  if (currentUser) {
    const headerActions = document.querySelector('.header-actions');
    if (headerActions) {
      headerActions.innerHTML = `
        <span style="margin-right: 1rem;">Welcome, ${currentUser.name}</span>
        <a href="${currentUser.role === 'doctor' ? 'doctor_dashboard.html' : currentUser.role === 'admin' ? 'admin.html' : 'profile.html'}" class="btn btn-primary">Profile</a>
        <button id="logout-btn" class="btn btn-outline">Logout</button>
      `;

      document.getElementById('logout-btn').addEventListener('click', () => {
        localStorage.removeItem('currentUser');
        window.location.href = 'index.html';
      });
    }
  }

  // Login
  const loginForm = document.getElementById('login-form');
  if (loginForm && !window.loginHandlerAttached) {
    window.loginHandlerAttached = true;
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      const email = loginForm.querySelector('input[type="email"]').value;
      const password = loginForm.querySelector('input[type="password"]').value;

      const users = JSON.parse(localStorage.getItem('users')) || [];
      const user = users.find(u => u.email === email && u.password === password);

      if (user) {
        localStorage.setItem('currentUser', JSON.stringify(user));
        alert(`✅ Welcome, ${user.name}`);

        // Redirect based on role
        if (user.role === 'doctor') {
          window.location.href = 'doctor_dashboard.html';
        } else if (user.role === 'admin') {
          window.location.href = 'admin.html';
        } else {
          window.location.href = 'index.html';
        }
      } else {
        alert('❌ Invalid credentials');
      }
    });
  }

  // Register
  const registerForm = document.getElementById('register-form');
  if (registerForm) {
    registerForm.addEventListener('submit', e => {
      e.preventDefault();

      const name = registerForm.querySelector('input[name="name"]').value;
      const email = registerForm.querySelector('input[name="email"]').value;
      const password = registerForm.querySelector('input[name="password"]').value;
      const role = registerForm.querySelector('select[name="role"]').value;

      if (role === 'admin' || (email === 'admin@gmail.com' && password === 'adminadmin')) {
        alert('❌ You cannot register as admin.');
        return;
      }

      if (!/\S+@\S+\.\S+/.test(email)) {
        alert("❌ Invalid email address.");
        return;
      }

      const users = JSON.parse(localStorage.getItem('users')) || [];

      if (users.find(u => u.email === email)) {
        alert('❌ Email already registered');
        return;
      }

      const newUser = { name, email, password, role };
      users.push(newUser);
      localStorage.setItem('users', JSON.stringify(users));
      alert("✅ Successfully registered!");
      window.location.href = 'login.html';
    });
  }

  // Booking Page Tabs
  document.querySelectorAll('.tabs li').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tabs li').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
    });
  });

  // Slot selection
  document.querySelectorAll('.slot').forEach(slot => {
    slot.addEventListener('click', () => {
      document.querySelectorAll('.slot').forEach(s => {
        s.style.background = '#eee';
        s.style.color = '#333';
      });
      slot.style.background = '#333';
      slot.style.color = '#fff';
    });
  });

  // Contact page form protection
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    if (!currentUser) {
      // Not logged in – disable form
      contactForm.innerHTML = `
        <p style="color: red; text-align: center; font-weight: bold;">
          You must <a href="login.html">login</a> to send a message.
        </p>
      `;
    } else {
      // Logged in – enable form submission
      contactForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const name = document.getElementById('contactName').value;
        const email = document.getElementById('contactEmail').value;
        const subject = document.getElementById('contactSubject').value;
        const message = document.getElementById('contactMessage').value;

        try {
          const res = await fetch('message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, subject, message })
          });

          const result = await res.json();
          if (result.status === 'success') {
            alert('✅ Message sent!');
            contactForm.reset();
          } else {
            alert('❌ ' + result.message);
          }
        } catch (err) {
          alert('❌ Failed to send message.');
        }
      });
    }
  }
});
