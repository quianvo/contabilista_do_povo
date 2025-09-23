-- ================================
-- Criação de tabelas e sequências
-- ================================

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS public.users (
    id integer NOT NULL PRIMARY KEY,
    name character varying(100),
    email character varying(100) UNIQUE,
    password character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

CREATE SEQUENCE IF NOT EXISTS public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    OWNED BY public.users.id;

ALTER TABLE public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS public.categories (
    id integer NOT NULL PRIMARY KEY,
    category character varying(50) NOT NULL UNIQUE,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

CREATE SEQUENCE IF NOT EXISTS public.categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    OWNED BY public.categories.id;

ALTER TABLE public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);

-- Tabela de contatos
CREATE TABLE IF NOT EXISTS public.contacts (
    id integer NOT NULL PRIMARY KEY,
    name character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    topic character varying(150) NOT NULL,
    content text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    viewed boolean DEFAULT false,
    telephone character varying
);

CREATE SEQUENCE IF NOT EXISTS public.contacts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    OWNED BY public.contacts.id;

ALTER TABLE public.contacts ALTER COLUMN id SET DEFAULT nextval('public.contacts_id_seq'::regclass);

-- Tabela de posts
CREATE TABLE IF NOT EXISTS public.posts (
    id integer NOT NULL PRIMARY KEY,
    title character varying(255),
    content text,
    category character varying(20) CHECK (category IN ('eventos','noticias','projectos','voluntariado')),
    rate integer DEFAULT 0,
    views integer DEFAULT 0,
    img text,
    user_id integer REFERENCES public.users(id) ON DELETE SET NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

CREATE SEQUENCE IF NOT EXISTS public.posts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    OWNED BY public.posts.id;

ALTER TABLE public.posts ALTER COLUMN id SET DEFAULT nextval('public.posts_id_seq'::regclass);

-- ================================
-- Inserindo dados iniciais
-- ================================

-- Usuário admin
INSERT INTO public.users (id, name, email, password, created_at) VALUES
(1, 'aceltino', 'ace@example.com', '$2y$10$5EoLtW0ea355KltIe8Gay.3r7vc7zbt2CcEeZWf.ueh9kM8t6AnSm', '2025-07-16 07:23:08')
ON CONFLICT (id) DO NOTHING;

-- Categorias
INSERT INTO public.categories (id, category, created_at) VALUES
(4, 'voluntariado', '2025-09-14 05:35:23.524894'),
(3, 'eventos', '2025-09-14 05:35:23.524894')
ON CONFLICT (id) DO NOTHING;

SELECT setval('public.categories_id_seq', 5, true);

-- Contatos
INSERT INTO public.contacts (id, name, email, topic, content, created_at, viewed, telephone) VALUES
(2, 'João Silva', 'joao@email.com', 'Suporte', 'Estou com dificuldade em acessar minha conta.', '2025-09-14 19:50:04.295014', true, NULL),
(3, 'João Silva', 'joao@email.com', 'Suporte', 'Estou com dificuldade em acessar minha conta.', '2025-09-15 17:43:29.834478', false, 'Estou com dificuldade em acessar minha conta.'),
(4, 'João Silva', 'joao@email.com', 'Suporte', 'Estou com dificuldade em acessar minha conta.', '2025-09-15 17:44:14.205155', false, 'Estou com dificuldade em acessar minha conta.'),
(5, 'João Silva', 'joao@email.com', 'Suporte', 'Estou com dificuldade em acessar minha conta.', '2025-09-15 17:46:01.840938', false, '944880743'),
(6, 'João Silva', 'joao@email.com', 'Suporte', 'Estou com dificuldade em acessar minha conta.', '2025-09-15 17:46:19.65134', false, '944880743'),
(7, 'João Silva', 'joao@email.com', 'Suporte', 'Estou com dificuldade em acessar minha conta.', '2025-09-15 18:11:20.28963', false, '944880743')
ON CONFLICT (id) DO NOTHING;

SELECT setval('public.contacts_id_seq', 7, true);

-- Posts
INSERT INTO public.posts (id, title, content, category, rate, views, img, user_id, created_at) VALUES
(28, 'Primeiro Teste Após a Última Actualização', 'Penso que está tudo bem, vamos experimentar', 'voluntariado', 0, 0, '/uploads/posts/post_687e4619e68277.02801165.jpg', 1, '2025-07-21 13:52:25'),
(29, 'Teste com PostGree', 'Este é o conteudo do teste', 'eventos', 1, 2, '/uploads/posts/post_68c268ea2ddb13.27238649.png', 1, '2025-09-11 06:15:06.220768'),
(31, 'Teste da Categoria', 'A categoria está testatada', 'eventos', 2, 2, '/uploads/posts/post_68c268ea2ddb13.27238649.png', 1, '2025-09-14 05:50:48.957685'),
(34, 'Teste da Categoria', 'A categoria está testatada', 'eventos', 2, 2, '/uploads/posts/post_68c268ea2ddb13.27238649.png', 1, '2025-09-14 05:50:48.957685'),
(35, 'Teste da Categoria', 'A categoria está testatada', 'eventos', 2, 2, '/uploads/posts/post_68c268ea2ddb13.27238649.png', 1, '2025-09-14 05:50:48.957685')
ON CONFLICT (id) DO NOTHING;

SELECT setval('public.posts_id_seq', 35, true);

-- Ajusta sequência de usuários
SELECT setval('public.users_id_seq', 1, true);
